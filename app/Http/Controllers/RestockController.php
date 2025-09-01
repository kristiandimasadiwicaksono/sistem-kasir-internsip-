<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Restock;
use App\Models\RestockDetail;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestockController extends Controller
{
    public function index () {
        $restocks = Restock::with(['supplier', 'details.produk'])
                    -> orderBy('tanggal','desc')
                    -> paginate(10);
        $suppliers = Supplier::all();
        $produk = Produk::all();

        return view('homepage.restock.index', compact('restocks','suppliers', 'produk'));
    }

    public function create (){
        $suppliers = Supplier::orderBy('nama')->get();
        $produk = Produk::orderBy('nama_produk')->get();

        return view('restock.create', compact('suppliers', 'produk'));
    }

    public function store (Request $request) {
        $request->validate([
            'id_supplier' => 'required|exists:supplier,id',
            'metode_pembayaran' => 'required|in:Tunai,Kredit',
            'products' => 'required|array|min:1',
            'products.*.id_produk' => 'required|exists:produk,id',
            'products.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $restock = Restock::create([
                'id_supplier' => $request->id_supplier,
                'tanggal' => now(),
                'metode_pembayaran' => $request->metode_pembayaran,
                'keterangan' => $request->keterangan,
                'status_pembayaran' => 'Belum Diterima Semua.',
            ]);

            foreach ($request->products as $item) {
                RestockDetail::create([
                    'id_restock'        => $restock->id,
                    'id_produk'         => $item['id_produk'],
                    'jumlah_dipesan'    => $item['jumlah_dipesan'],
                    'jumlah_diterima'   => 0
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
            'id_produk' => 'required|exists:produk,id',
            'jumlah_diterima' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $id){
            $restockDetail = RestockDetail::where('id_restock', $id)
                                ->where('id_produk', $request->id_produk)
                                ->firstOrFail();

            $orderedQuantity  = $restockDetail->jumlah_dipesan;
            $receivedQuantity = $restockDetail->jumlah_diterima + $request->jumlah_diterima;

            if ($receivedQuantity > $orderedQuantity) {
                return back()->with('error', 'Jumlah yang diterima melebihi jumlah yang dipesan');
            }

            $restockDetail->update(['jumlah_diterima' => $receivedQuantity]);
            $restockDetail->produk->increment('stok', $request->jumlah_diterima);

            $restock = Restock::with('details')->findOrFail($id);
            $isFullyReceived = true;
            foreach ($restock->details as $detail) {
                if ($detail->jumlah_diterima < $detail->jumlah_dipesan) {
                    $isFullyReceived = false;
                    break;
                }
            }
            
            if($isFullyReceived) {
                $restock->update(['status_penerimaan' => 'Sudah Diterima Semua']);
            }else{
                $restock->update(['status_penerimaan' => 'Penerimaan Parsial']);
            }
        });
        return back()->with('success', 'Penerimaan barang berhasil dicatat dan stok telah diperbarui');
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
}
