<?php

namespace App\Http\Controllers;

use App\Models\HistoryPenjualan;
use Illuminate\Http\Request;

class HistoryPenjualanController extends Controller
{
    public function index() {
        $history = HistoryPenjualan::with(['produk', 'penjualan'])
                    ->orderBy('tanggal','desc')
                    ->get()
                    ->groupBy('id_penjualan');
        return view('homepage.penjualan.history.index', compact('history'));
    }
}
