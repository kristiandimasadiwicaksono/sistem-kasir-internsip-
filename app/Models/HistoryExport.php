<?php

namespace App\Models;

use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HistoryExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection() {
        $history = HistoryPenjualan::with('produk')
                    ->orderBy('tanggal','desc')
                    ->get();

        return $history->values()->map(function ($item, $index){
            return [
                'No'              => $index + 1,
                'Kode Transaksi'  => $item->kode_transaksi,
                'Tanggal Beli'    => $item->tanggal ?? '-',
                'Tanggal Batal'   => $item->tanggal_batal ?? '-',
                'Produk'          => optional($item->produk)->nama_produk ?? '-',
                'Jumlah'          => $item->jumlah,
                'Status'          => $item->status,
            ];
        });
    }

    public function headings(): array {
        return [
            'No',
            'Kode Transaksi',
            'Tanggal Beli',
            'Tanggal Batal',
            'Produk',
            'Jumlah',
            'Status',
        ];
    }

    public function styles(Worksheet $sheet) {
        // Header tebal + center + background abu-abu
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE5E5E5'],
            ],
        ]);

        // Auto size semua kolom
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Border tipis semua tabel
        $sheet->getStyle('A1:G' . $sheet->getHighestRow())
              ->getBorders()
              ->getAllBorders()
              ->setBorderStyle(Border::BORDER_THIN);

        // Rata tengah kolom tertentu
        $sheet->getStyle('A2:A' . $sheet->getHighestRow())
              ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:F' . $sheet->getHighestRow())
              ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G2:G' . $sheet->getHighestRow())
              ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Zebra striping baris (warna selang-seling)
        for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {
            if ($row % 2 == 0) {
                $sheet->getStyle("A{$row}:G{$row}")->getFill()
                      ->setFillType(Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('FFF9F9F9');
            }
        }

        return [];
    }
}
