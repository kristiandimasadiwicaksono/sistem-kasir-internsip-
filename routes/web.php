<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PenjualanDetailController;
use App\Http\Controllers\HistoryPenjualanController;
use App\Http\Controllers\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
}) ->name('login')->middleware('guest');

Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');

Route::get('auth/google', [LoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

Route::get('/change-password', function() {
    return view('auth.change-password');
})->middleware('auth')->name('user.change-password');

Route::post('/update-password', [LoginController::class, 'updatePassword'])->middleware('auth')->name('user.update-password');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group( function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('produk', ProdukController::class);
    Route::resource('penjualan', PenjualanController::class)->only(['index', 'create', 'store', 'destroy']);
    
    Route::prefix('penjualan/{id_penjualan}')->group(function () {
        Route::get('detail', [PenjualanDetailController::class, 'index'])->name('penjualan.detail');
        Route::get('detail/create', [PenjualanDetailController::class, 'create'])->name('penjualan.detail.create');
        Route::post('detail', [PenjualanDetailController::class, 'store'])->name('penjualan.detail.store');
        Route::get('detail/{id_detail}/edit', [PenjualanDetailController::class, 'edit'])->name('penjualan.detail.edit');
        Route::put('detail/{id_detail}', [PenjualanDetailController::class, 'update'])->name('penjualan.detail.update');
        Route::delete('detail/{id_detail}', [PenjualanDetailController::class, 'destroy'])->name('penjualan.detail.destroy');
    });

    Route::get('/penjualan/history', [HistoryPenjualanController::class, 'index'])->name('penjualan.history');
    Route::get('penjualan/{id}/print', [PenjualanController::class, 'print'])->name('penjualan.print');
    Route::get('penjualan/{id_penjualan}/detail/json', [PenjualanDetailController::class, 'json'])->name('penjualan.detail.json');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('admin', AdminController::class)->only(['index', 'edit', 'update', 'destroy']);
});


