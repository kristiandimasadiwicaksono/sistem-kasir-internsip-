<?php

namespace App\Models;

use App\Models\Restock;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RestockExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $rows = collect();

        $restocks = Restock::with(['supplier', 'details.produk'])->get();

        foreach ($restocks as $restock) {
            foreach ($restock->details as $detail) {
                // ✅ Hitung retur khusus untuk produk ini
                $jumlahRetur = DB::table('retur_barang_detail')
                    ->join('retur_barang', 'retur_barang.id', '=', 'retur_barang_detail.id_retur')
                    ->where('retur_barang.id_restock', $restock->id)
                    ->where('retur_barang_detail.id_produk', $detail->id_produk)
                    ->sum('retur_barang_detail.jumlah_retur');

                // Pastikan hasilnya integer 0, bukan null/kosong
                $jumlahRetur = intval($jumlahRetur ?? 0);

                $rows->push([
                    'Tanggal'   => $restock->tanggal,
                    'Supplier'  => $restock->supplier->nama ?? '-',
                    'Produk'    => $detail->produk->nama_produk ?? '-',
                    'Dipesan'   => intval($detail->jumlah_dipesan ?? 0),
                    'Diterima'  => intval($detail->jumlah_diterima ?? 0),
                    'Retur' => (string) intval($jumlahRetur ?? 0),
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Tanggal', 'Supplier', 'Produk', 'Dipesan', 'Diterima', 'Retur'
        ];
    }
}
