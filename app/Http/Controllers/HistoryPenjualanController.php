<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoryExport;
use App\Models\HistoryPenjualan;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class HistoryPenjualanController extends Controller
{
    public function index() {
        $history = HistoryPenjualan::with(['produk', 'penjualan'])
                    ->orderBy('tanggal','desc')
                    ->get()
                    ->groupBy('id_penjualan');
                    
        return view('homepage.penjualan.history.index', compact('history'));
    }

    public function exportExcel() {
        return Excel::download(new HistoryExport, 'history_penjualan.xlsx');
    }
}
