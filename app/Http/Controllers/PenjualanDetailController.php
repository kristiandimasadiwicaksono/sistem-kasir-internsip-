<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use App\Models\HistoryPenjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenjualanDetailController extends Controller
{
    public function index($id_penjualan)
    {
        $penjualan = Penjualan::with('details.produk')->findOrFail($id_penjualan);
        return view('homepage.penjualan.detail', compact('penjualan'));
    }

    public function create($id_penjualan)
    {
        $penjualan = Penjualan::findOrFail($id_penjualan);
        $produk    = Produk::all();

        return view('homepage.penjualan.detail.create', compact('penjualan','produk'));
    }

    public function destroy($id_penjualan, $id_detail)
    {
        DB::transaction(function () use ($id_penjualan, $id_detail) {
            $detail    = PenjualanDetail::findOrFail($id_detail);
            $penjualan = Penjualan::findOrFail($id_penjualan);

            $penjualan->decrement('total', $detail->subtotal ?? 0);
            $detail->delete();

            // ✅ Hapus dari history juga
            HistoryPenjualan::where('id_penjualan', $penjualan->id)
                            ->where('id_produk', $detail->id_produk)
                            ->delete();
        });

        return redirect()->route('penjualan.detail', $id_penjualan)->with('success', 'Detail penjualan berhasil dihapus');
    }

    public function json($id_penjualan)
    {
        $penjualan = Penjualan::with('details.produk')->find($id_penjualan);
        if (!$penjualan) {
            return response()->json(['message' => 'Penjualan tidak ditemukan'], 404);
        }

        $payload = [
            'id' => $penjualan->id,
            'tanggal' => (string)$penjualan->tanggal,
            'total' => $penjualan->total,
            'details' => $penjualan->details->map(function ($d) {
                return [
                    'id' => $d->id,
                    'id_produk' => $d->id_produk,
                    'nama_produk' => optional($d->produk)->nama_produk,
                    'jumlah' => $d->jumlah,
                    'harga' => $d->harga ?? optional($d->produk)->harga,
                    'subtotal' => $d->subtotal,
                ];
            })->values(),
        ];

        return response()->json($payload);
    }
}
