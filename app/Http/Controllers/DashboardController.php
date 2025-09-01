<?php

namespace App\Http\Controllers;

use App\Models\HistoryPenjualan;
use App\Models\Penjualan;
use App\Models\Produk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index() {
        $today = Carbon::today(config('app.timezone'));

        $penjualanHariIni = HistoryPenjualan::where('status', 'SUCCESS')
            ->whereDate('tanggal', today())
            ->distinct('kode_transaksi')
            ->count('kode_transaksi');
    
        $totalProduk = Produk::count();

        $lowStockProducts = Produk::where('stok', '<=', 5)->get();

        $totalTransaksi = HistoryPenjualan::distinct('kode_transaksi')->count('kode_transaksi');
        
        $penjualanBulanIni = HistoryPenjualan::where('status', 'SUCCESS')
                            ->whereMonth('tanggal', Carbon::now()->month)
                            ->whereYear('tanggal', Carbon::now()->year)
                            ->join('produk', 'history_penjualan.id_produk', '=', 'produk.id')
                            ->selectRaw('SUM(history_penjualan.jumlah * produk.harga) as total')
                            ->value('total');

        $topProduk = DB::table('penjualan_detail as pd')
                    ->join('produk as p', 'pd.id_produk', '=', 'p.id')
                    ->select('p.nama_produk', DB::raw('SUM(pd.jumlah) as total_terjual'))
                    ->groupBy('pd.id_produk', 'p.nama_produk')
                    ->orderByDesc('total_terjual')
                    ->limit(5)
                    ->get();

        $aktivitasTerbaru = HistoryPenjualan::with('penjualan')
                            ->orderByDesc('tanggal')
                            ->limit(2)
                            ->get();

        return view('homepage.index', compact(
            'penjualanHariIni',
            'totalProduk',
            'totalTransaksi',
            'penjualanBulanIni',
            'topProduk',
            'aktivitasTerbaru',
            'lowStockProducts'
        ));
    }
}
