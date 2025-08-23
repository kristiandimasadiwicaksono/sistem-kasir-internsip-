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

    public function store(Request $request, $id_penjualan)
    {
        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.id_produk' => 'required|exists:produk,id',
            'products.*.jumlah' => 'required|integer|min:0', // boleh 0
        ]);

        $aggregated = [];
        foreach ($request->products as $row) {
            $id = $row['id_produk'] ?? null;
            $j  = (int) ($row['jumlah'] ?? 0);
            if (! $id) continue;
            if (! isset($aggregated[$id])) $aggregated[$id] = 0;
            $aggregated[$id] += $j;
        }

        DB::transaction(function () use ($aggregated, $id_penjualan) {
            $penjualan = Penjualan::findOrFail($id_penjualan);

            foreach ($aggregated as $id_produk => $qtyToAdd) {
                $produk = Produk::findOrFail($id_produk);

                $existing = PenjualanDetail::where('id_penjualan', $penjualan->id)
                            ->where('id_produk', $produk->id)
                            ->first();

                $existingJumlah = $existing ? (int)$existing->jumlah : 0;
                $stokTersedia = $produk->stok ?? 0;

                $qtyFinal = min($qtyToAdd + $existingJumlah, $stokTersedia) - $existingJumlah;
                if ($qtyFinal <= 0) continue;

                $newJumlah = $existingJumlah + $qtyFinal;
                $subtotalBaru = $produk->harga * $newJumlah;

                if ($existing) {
                    $penjualan->decrement('total', $existing->subtotal ?? 0);

                    $existing->update([
                        'jumlah'   => $newJumlah,
                        'subtotal' => $subtotalBaru,
                    ]);

                    $penjualan->increment('total', $subtotalBaru);
                } else {
                    PenjualanDetail::create([
                        'id_penjualan' => $penjualan->id,
                        'id_produk'    => $produk->id,
                        'jumlah'       => $qtyFinal,
                        'subtotal'     => $subtotalBaru,
                    ]);

                    $penjualan->increment('total', $subtotalBaru);
                }

                HistoryPenjualan::updateOrCreate(
                    [
                        'id_penjualan' => $penjualan->id,
                        'id_produk'    => $produk->id,
                    ],
                    [
                        'jumlah'        => $newJumlah,
                        'tanggal'       => $penjualan->tanggal,
                        'tanggal_batal' => $penjualan->tanggal_batal,
                        'status'        => 'SUCCESS',
                    ]
                );
            }
        });

        return redirect()->route('penjualan.detail', $id_penjualan)
                        ->with('success','Produk berhasil ditambahkan ke penjualan');
    }

    public function edit($id_penjualan, $id_detail) {
        $penjualan = Penjualan::findOrFail($id_penjualan);
        $detail    = PenjualanDetail::with('produk')->findOrFail($id_detail);
        $produk    = Produk::orderBy('nama_produk')->get();

        return view('homepage.penjualan.detail.edit', compact('penjualan','detail','produk'));
    }

    public function update(Request $request, $id_penjualan, $id_detail)
    {
        $detail = PenjualanDetail::findOrFail($id_detail);
        $request->validate([
            'id_produk' => 'required|exists:produk,id',
            'jumlah' => 'required|integer|min:0', // boleh 0
        ]);

        DB::transaction(function () use ($request, $id_penjualan, $id_detail) {
            $penjualan = Penjualan::findOrFail($id_penjualan);
            $detail    = PenjualanDetail::findOrFail($id_detail);

            $produkBaru = Produk::findOrFail($request->id_produk);
            $newJumlah  = (int)$request->jumlah;

            $stokTotal = ($produkBaru->stok ?? 0) + $detail->jumlah;
            if ($newJumlah > $stokTotal) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'jumlah' => ["Jumlah tidak boleh melebihi total stok yang tersedia ({$stokTotal})."]
                ]);
            }

            $penjualan->decrement('total', $detail->subtotal ?? 0);
            $subtotalBaru = ($produkBaru->harga ?? 0) * $newJumlah;

            $detail->update([
                'id_produk' => $produkBaru->id,
                'jumlah'    => $newJumlah,
                'subtotal'  => $subtotalBaru,
            ]);

            $penjualan->increment('total', $subtotalBaru);

            HistoryPenjualan::updateOrCreate(
                [
                    'id_penjualan' => $penjualan->id,
                    'id_produk'    => $produkBaru->id,
                ],
                [
                    'kode_transaksi'=> $penjualan->kode_transaksi,
                    'jumlah'        => $newJumlah,
                    'tanggal'       => $penjualan->tanggal,
                    'tanggal_batal' => $penjualan->tanggal_batal,
                    'status'        => 'SUCCESS',
                ]
            );
        });

        return redirect()->route('penjualan.detail', $id_penjualan)->with('success', 'Detail penjualan berhasil diupdate');
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
