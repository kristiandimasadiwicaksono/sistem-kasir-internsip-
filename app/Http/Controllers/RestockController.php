<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Produk;
use App\Models\Restock;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\RestockDetail;
use App\Models\RestockExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ReturBarang; // Added for the new retur method
use App\Models\ReturBarangDetail; // Added for the new retur method

class RestockController extends Controller
{
    public function index () {
        $restocks = Restock::with(['supplier', 'details.produk'])
            ->orderBy('tanggal','desc')
            ->paginate(10);

        $suppliers = Supplier::all();
        $produk = Produk::all();

        $history = RestockDetail::with(['produk', 'restock.supplier'])
            ->get()
            ->map(function ($detail) {
                $jumlahRetur = ReturBarangDetail::whereHas('retur', function($q) use ($detail) {
                    $q->where('id_restock', $detail->id_restock);
                })
                ->where('id_produk', $detail->id_produk)
                ->sum('jumlah_retur');

                return [
                    'id_restock'      => $detail->id_restock,
                    'tanggal'         => $detail->restock->tanggal,
                    'supplier'        => $detail->restock->supplier->nama,
                    'produk'          => $detail->produk->nama_produk,
                    'jumlah_dipesan'  => $detail->jumlah_dipesan,
                    'jumlah_diterima' => $detail->jumlah_diterima,
                    'jumlah_retur'    => $jumlahRetur,
                ];
            });

        return view('homepage.restock.index', compact('restocks','suppliers', 'produk', 'history'));
    }

    public function create (){
        $suppliers = Supplier::orderBy('nama')->get();
        $produk = Produk::orderBy('nama_produk')->get();

        return view('restock.create', compact('suppliers', 'produk'));
    }

    public function store (Request $request) {
        $request->validate([
            'id_supplier' => 'required|exists:supplier,id',
            'status_pembayaran' => 'required|in:LUNAS,BELUM_LUNAS',
            'products' => 'required|array|min:1',
            'products.*.id_produk' => 'required|exists:produk,id',
            'products.*.jumlah_dipesan' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $restock = Restock::create([
                'id_supplier'       => $request->id_supplier,
                'tanggal'           => now(),
                'status_pembayaran' => $request->status_pembayaran,
            ]);

            foreach ($request->products as $item) {
                RestockDetail::create([
                    'id_restock'        => $restock->id,
                    'id_produk'         => $item['id_produk'],
                    'jumlah_dipesan'    => $item['jumlah_dipesan'],
                    'jumlah_diterima'   => 0,
                    'status_penerimaan' => 'BELUM_DITERIMA'
                ]);
            }
        });
        
        return redirect()->route('restock.index')->with('success', 'Pesanan restock berhasil dibuat. Stok akan bertambah setelah barang diterima.');
    }

    public function show($id) {
        $restock = Restock::with(['supplier','details.produk'])->findOrFail($id);
        return view ('restock.show', compact('restock'));
    }

    public function receive(Request $request, $id) {
        $request->validate([
            'id_produk' => 'required|array',
            'id_produk.*' => 'exists:produk,id',
            'jumlah_diterima' => 'required|array',
            'jumlah_diterima.*' => 'integer|min:1',
        ]);

        DB::transaction(function () use ($request, $id){
            $restock = Restock::with('details.produk')->findOrFail($id);
            
            foreach ($request->id_produk as $index => $produkId) {
                $jumlah = $request->jumlah_diterima[$index];

                $restockDetail = RestockDetail::where('id_restock', $id)
                    ->where('id_produk', $produkId)
                    ->firstOrFail();

                $orderedQuantity = $restockDetail->jumlah_dipesan;
                $receivedQuantity = $restockDetail->jumlah_diterima + $jumlah;

                if ($receivedQuantity > $orderedQuantity) {
                    throw new \Exception("Jumlah diterima melebihi jumlah dipesan untuk produk {$restockDetail->produk->nama_produk}");
                }

                $status = ($receivedQuantity >= $orderedQuantity) ? 'SELESAI' : 'SEBAGIAN';
                
                $restockDetail->update([
                    'jumlah_diterima' => $receivedQuantity,
                    'status_penerimaan' => $status
                ]);
                
                $returTerpakai = ReturBarangDetail::whereHas('retur', function($q) use ($restockDetail) {
                    $q->where('id_restock', $restockDetail->id_restock);
                })
                ->where('id_produk', $produkId)
                ->sum('jumlah_retur');

            if ($returTerpakai > 0) {
                $dikurangi = min($jumlah, $returTerpakai);

                ReturBarangDetail::whereHas('retur', function($q) use ($restockDetail) {
                        $q->where('id_restock', $restockDetail->id_restock);
                    })
                    ->where('id_produk', $produkId)
                    ->orderBy('id', 'desc')
                    ->limit(1)
                    ->decrement('jumlah_retur', $dikurangi);
            }
                $restockDetail->produk->increment('stok', $jumlah);
            }
        });

        return back()->with('success', 'Penerimaan barang berhasil dicatat dan stok diperbarui.');
    }

    // Updated method for handling returns
    public function retur(Request $request, $id) {
        $request->validate([
            'alasan_retur' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'id_produk' => 'required|array',
            'id_produk.*' => 'required|integer',
            'jumlah_retur' => 'required|array',
            'jumlah_retur.*' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $restock = Restock::with('details')->findOrFail($id);

            // ✅ Minimal ada barang diterima
            if ($restock->details->sum('jumlah_diterima') <= 0) {
                throw new \Exception("Tidak ada barang yang bisa diretur.");
            }

            $returBarang = ReturBarang::create([
                'id_restock'    => $id,
                'tanggal_retur' => now(),
                'alasan_retur'  => $request->alasan_retur,
                'catatan'       => $request->catatan,
            ]);

            foreach ($request->id_produk as $index => $id_produk) {
                $jumlah_retur = $request->jumlah_retur[$index];

                $restockDetail = RestockDetail::where('id_restock', $id)
                    ->where('id_produk', $id_produk)
                    ->firstOrFail();
                $produk = Produk::findOrFail($id_produk);

                // ✅ Batas retur = jumlah diterima saat ini
                if ($jumlah_retur > $restockDetail->jumlah_diterima) {
                    throw new \Exception("Jumlah retur untuk {$produk->nama_produk} melebihi jumlah diterima.");
                }

                ReturBarangDetail::create([
                    'id_retur' => $returBarang->id,
                    'id_produk' => $id_produk,
                    'jumlah_retur' => $jumlah_retur,
                ]);

                // ✅ Kurangi stok produk
                $produk->decrement('stok', $jumlah_retur);

                // ✅ Kurangi jumlah diterima di detail
                $restockDetail->decrement('jumlah_diterima', $jumlah_retur);
            }

            // ✅ Kalau semua sudah diretur → ubah status restock
            if ($restock->details->sum('jumlah_diterima') <= 0) {
                $restock->update(['status' => 'RETUR']);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Barang berhasil di-retur.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal memproses retur. ' . $e->getMessage());
        }
    }

    public function destroy($id) {
        DB::transaction(function () use ($id) {
            $restock = Restock::with('details')->findOrFail($id);

            foreach ($restock->details as $detail) {
                if ($detail->jumlah_diterima > 0) {
                    $detail->produk->decrement('stok', $detail->jumlah_diterima);
                }
            }

            $restock->details()->delete();
            $restock->delete();
        });
        return redirect()->route('restock.index')->with('success', 'Restock berhasil dihapus.');
    }

    public function export() {
        return Excel::download(new RestockExport, 'riwayat-restock.xlsx');
    }
}