<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\HistoryPenjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    // Tampilkan semua penjualan (kirim produk juga karena view mengandung modal add/edit)
    public function index()
    {
        $penjualan = Penjualan::with('details.produk')->orderBy('tanggal','desc')->paginate(10);
        $produk = Produk::orderBy('nama_produk')->get();
        return view('homepage.penjualan.index', compact('penjualan', 'produk'));
    }

    // Form buat tambah penjualan
    public function create()
    {
        $produk = Produk::all();
        return view('homepage.penjualan.create', compact('produk'));
    }

    // Simpan penjualan baru
    public function store(Request $request)
    {
        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.id_produk' => 'required|exists:produk,id',
            'products.*.jumlah'     => 'required|integer|min:1',
        ]);

        // server-side stok validation sebelum transaksi
        $stockErrors = [];
        foreach ($request->products as $idx => $item) {
            $produk = Produk::find($item['id_produk']);
            if (!$produk) {
                $stockErrors["products.$idx.id_produk"] = "Produk tidak ditemukan.";
                continue;
            }

            $stokTersedia = (int) ($produk->stok ?? 0);
            $jumlahMinta  = (int) ($item['jumlah'] ?? 0);

            if ($stokTersedia <= 0) {
                $stockErrors["products.$idx.id_produk"] = "Produk '{$produk->nama_produk}' stok habis.";
            } elseif ($stokTersedia < $jumlahMinta) {
                $stockErrors["products.$idx.jumlah"] = "Stok untuk '{$produk->nama_produk}' tidak cukup. Tersedia: {$stokTersedia}.";
            }
        }

        if (!empty($stockErrors)) {
            // kembali ke form dengan input lama dan error spesifik
            return redirect()->back()->withInput()->withErrors($stockErrors);
        }

        $penjualan = DB::transaction(function () use ($request) {
            $penjualan = Penjualan::create([
                'tanggal' => now(),
                'total'   => 0,
                'status' => 'PENDING' // ✅ Status awal PENDING
            ]);

            $total = 0;

            // Simpan detail penjualan (TANPA mengurangi stok)
            foreach ($request->products as $item) {
                $produk   = Produk::findOrFail($item['id_produk']);
                $subtotal = $produk->harga * $item['jumlah'];
                $total   += $subtotal;

                PenjualanDetail::create([
                    'id_penjualan' => $penjualan->id,
                    'id_produk'    => $produk->id,
                    'jumlah'       => $item['jumlah'],
                    'subtotal'     => $subtotal,
                ]);
            }

            // 3. Update total transaksi di header
            $penjualan->update(['total' => $total]);

            // 4. Tambahkan entry ke history_penjualan
            foreach ($request->products as $item) {
                $produk = Produk::findOrFail($item['id_produk']);
            
                HistoryPenjualan::create([
                    'id_penjualan' => $penjualan->id,
                    'kode_transaksi' => $penjualan->kode_transaksi,
                    'id_produk' => $produk->id,
                    'jumlah' => $item['jumlah'],
                    'status' => 'PENDING', // Status awal selalu PENDING
                    'tanggal' => now(),
                    'tanggal_batal' => null
                ]);
            }
                    
            return $penjualan;
        });

        return redirect()->route('checkout', $penjualan->id);
    }

    public function print($id)
    {
        $penjualan = Penjualan::with('details.produk')->findOrFail($id);
        return view('homepage.penjualan.print', compact('penjualan'));
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $penjualan = Penjualan::with('details')->findOrFail($id);

            // kembalikan stok jika status SUCCESS
            if ($penjualan->status === 'SUCCESS') {
                foreach ($penjualan->details as $detail) {
                    $produk = $detail->produk;
                    if ($produk) {
                        $produk->increment('stok', $detail->jumlah);
                    }
                }
            }

            // Update history jadi canceled
            HistoryPenjualan::where('id_penjualan', $penjualan->id)
                ->whereIn('status', ['SUCCESS','PENDING'])
                ->update([
                    'status' => 'CANCELED',
                    'tanggal_batal' => now()
                ]);

            // setelah history dicatat, baru hapus penjualan
            $penjualan->delete();
        });

        return redirect()->route('penjualan.index')
                        ->with('success', 'Data berhasil dihapus dan stok dikembalikan');
    }

    public function history()
    {
        // ambil data penjualan lengkap sama relasinya
        $history = HistoryPenjualan::with(['produk', 'penjualan'])
            ->orderBy('tanggal','desc')
            ->get()
            ->groupBy('id_penjualan');
        
        // kirim ke view
        return view('homepage.penjualan.history.index', compact('history'));
    }
}