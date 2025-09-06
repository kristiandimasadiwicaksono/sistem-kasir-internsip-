<?php

use App\Models\HistoryExport;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RestockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\PenjualanDetailController;
use App\Http\Controllers\HistoryPenjualanController;

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
    return redirect()->route('login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

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
    Route::resource('produk', ProdukController::class)->except(['show']);
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

Route::get('/payment/{id}/edit', [PaymentController::class, 'edit'])->name('payment-edit');
Route::get('/payment/checkout/{id}', [PaymentController::class, 'checkout'])->name('checkout');
Route::post('/payment/cash/{id}', [PaymentController::class, 'cash'])->name('payment.cash');
Route::post('/payment/notification', [PaymentController::class, 'notification'])->name('payment.notification');
Route::post('/payment/cashless/{id}/success', [PaymentController::class, 'cashlessSuccess'])->name('payment.cashless.success');
Route::post('/payment/{penjualan}/store', [PaymentController::class, 'store'])->name('payment.store');
Route::get('/payment/success/{id}', [PaymentController::class, 'successPage'])->name('payment.success');
Route::middleware(['auth'])->group(function () {
    Route::get('/restock/export', [RestockController::class, 'export'])->name('restock.export');
    Route::resource('restock', RestockController::class)->except(['edit', 'update']);
    Route::post('/restock/{id}/receive', [RestockController::class, 'receive'])->name('restock.receive');
    Route::post('/restock/{id}/retur', [RestockController::class, 'retur'])->name('restock.return');
    Route::get('/restock/history/all', [RestockController::class, 'historyAll'])->name('restock.historyAll');
});
Route::get('/supplier/index', [SupplierController::class, 'index'])->name('suppliers.index');
Route::get('/supplier/create', [SupplierController::class, 'create'])->name('suppliers.create');
Route::post('/supplier', [SupplierController::class, 'store'])->name('suppliers.store');
Route::delete('/supplier/{id}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
Route::get('/supplier/{id}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
Route::put('/supplier/{id}', [SupplierController::class, 'update'])->name('suppliers.update');

Route::post('/produk/import', [ProdukController::class, 'import'])->name('produk.import.store');
Route::get('/produk/import', function() {
    return view('homepage.produk.import'); // ini view form upload
})->name('produk.import');

Route::get('/history/export/excel', [HistoryPenjualanController::class, 'exportExcel'])->name('history.export.excel');

Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
