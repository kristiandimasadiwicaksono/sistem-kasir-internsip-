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
            'products.*.jumlah'    => 'required|integer|min:1',
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

        DB::transaction(function () use ($request) {
            // 1. Buat header penjualan
            $penjualan = Penjualan::create([
                'tanggal' => now(),
                'total'   => 0
            ]);

            $total = 0;

            // 2. Simpan detail penjualan
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

                // jika tidak pakai TRIGGER: jangan lupa decrement stok di sini
                // $produk->decrement('stok', $item['jumlah']);
            }

            // 3. Update total transaksi di header
            $penjualan->update(['total' => $total]);

            foreach ($request->products as $item) {
                $produk = Produk::findOrFail($item['id_produk']);
            
                HistoryPenjualan::create([
                    'id_penjualan' => $penjualan->id,
                    'id_produk' => $produk->id,
                    'jumlah' => $item['jumlah'],
                    'status' => 'SUCCESS',
                    'tanggal' => now(),
                    'tanggal_batal' => null
                ]);
            }
        });

        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil disimpan!');
    }

    // Print view (safe find)
    public function print($id)
    {
        $penjualan = Penjualan::with('details.produk')->findOrFail($id);
        return view('homepage.penjualan.print', compact('penjualan'));
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $penjualan = Penjualan::with('details')->findOrFail($id);

            foreach ($penjualan->details as $detail) {
                // kembalikan stok kalau produk masih ada
                $produk = Produk::find($detail->id_produk);
                if ($produk) {
                    $produk->increment('stok', $detail->jumlah);
                }

                        // update history jadi canceled
                $history = HistoryPenjualan::where('id_penjualan', $penjualan->id)
                            ->where('id_produk', $detail->id_produk)
                            ->where('status', 'SUCCESS')
                            ->first();

                if ($history) {
                    $history->update([
                        'status' => 'CANCELED',
                        'tanggal_batal' => now()
                    ]);
                }
            }

            // setelah history dicatat, baru hapus penjualan
            $penjualan->delete();
        });

        return redirect()->route('penjualan.index')
                        ->with('success', 'Data berhasil dihapus dan stok dikembalikan');
    }

    public function history()
    {
        // ambil data penjualan lengkap sama relasinya
        $history = Penjualan::with(['details.produk'])->get();

        // kirim ke view
        return view('homepage.penjualan.history.index', compact('history'));
    }
}
