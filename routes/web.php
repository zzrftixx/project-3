<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\LaporanPenjualanController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServiceInstallationController;
use App\Http\Controllers\Admin\InstallationRequestController as AdminInstallationRequestController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\Auth\SalesReportController;
use App\Models\Komponen;
use App\Http\Controllers\RatingController;

// ------------------- PUBLIC ROUTES -------------------
Route::get('/', function () { return view('index'); })->name('home');
Route::get('/index', function () { return view('index'); })->name('index');
Route::get('/about', function () { return view('about'); })->name('about');
Route::get('/service', function () { return view('service.index'); })->name('service');
Route::get('/contact', function () { return view('contact'); })->name('contact');
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [PaymentController::class, 'showCheckout'])->name('checkout');
    Route::post('/checkout', [PaymentController::class, 'processCheckout'])->name('checkout.process');
    Route::get('/payment', [PaymentController::class, 'showPayment'])->name('payment.page');
});

// Service Installation (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/service/installation', [ServiceInstallationController::class, 'index'])->name('service.installation.index');
    Route::post('/service/installation', [ServiceInstallationController::class, 'store'])->name('service.installation.store');
});

// Produk
Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
Route::get('/produk/cari', [ProdukController::class, 'cari'])->name('produk.cari');
Route::get('/produk/{id}', [BarangController::class, 'show'])->name('produk.show');

// Rating routes
    Route::post('/rating', [RatingController::class, 'store'])->middleware('auth')->name('rating.store');
Route::get('/rating/{barang_id}', [RatingController::class, 'show']);
    

// Paket (public)
Route::get('/paket', [PaketController::class, 'publicIndex'])->name('paket.index');
Route::get('/paket/{id}/detail', [PaketController::class, 'show'])->name('paket.detail');

// Komponen (public/user)
Route::get('/komponen', function () {
    $komponens = Komponen::all();
    return view('komponen.index', compact('komponens'));
})->name('komponen.index');
// Detail komponen (partial HTML untuk modal)
Route::get('/komponen/{id}', function ($id) {
    $komponen = Komponen::findOrFail($id);
    return view('komponen.detail-partial', compact('komponen'));
});

// Keranjang
Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index')->middleware('auth');
Route::post('/keranjang/tambah', [CartController::class, 'addToCart'])->name('cart.add')->middleware('auth');
Route::delete('/keranjang/{id}', [CartController::class, 'remove'])->name('cart.remove')->middleware('auth');
Route::patch('/keranjang/{id}', [CartController::class, 'update'])->name('cart.update')->middleware('auth');

// Payment
Route::post('/payment', [PaymentController::class, 'createTransaction'])->name('payment.create');
Route::post('/payment/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');

// ------------------- AUTH ROUTES -------------------
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login']);
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [RegisterController::class, 'processRegister'])->name('register.post');
        Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('forgot.password');
        Route::post('/forgot-password', [ForgotPasswordController::class, 'processForgot'])->name('forgot.password.post');
        Route::get('/get-your-code', [ForgotPasswordController::class, 'showOtpForm'])->name('get.your.code');
        Route::post('/get-your-code', [ForgotPasswordController::class, 'verifyOtp'])->name('verify.code');
        Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('reset.password');
        Route::post('/reset-password', [ForgotPasswordController::class, 'processReset'])->name('reset.password.post');

        // Google OAuth
        Route::get('/auth/google', [App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('auth.google');
        Route::get('/auth/google/callback', [App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback']);

// ------------------- ADMIN ROUTES -------------------
Route::delete('admin/databarang/bulk', [BarangController::class, 'bulkDestroy'])->name('admin.databarang.bulkDestroy');

Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Barang CRUD
    Route::prefix('databarang')->name('databarang.')->group(function () {
        Route::get('/', [BarangController::class, 'index'])->name('index');
        Route::get('/create', function() { return view('admin.barang.create'); })->name('create');
        Route::post('/', [BarangController::class, 'store'])->name('store');
        Route::get('/{barang}/edit', function(\App\Models\Barang $barang) { return view('admin.barang.edit', compact('barang')); })->name('edit');
        Route::put('/{barang}', [BarangController::class, 'update'])->name('update');
        Route::delete('/{barang}', [BarangController::class, 'destroy'])->name('destroy');
    });

    // Paket CRUD
    Route::prefix('paket')->name('paket.')->group(function () {
        Route::get('/', [PaketController::class, 'index'])->name('index');
        Route::post('/', [PaketController::class, 'store'])->name('store');
        Route::get('/create', [PaketController::class, 'create'])->name('create');
        Route::get('/{paket}/edit', [PaketController::class, 'edit'])->name('edit');
        Route::put('/{paket}', [PaketController::class, 'update'])->name('update');
        Route::delete('/{paket}', [PaketController::class, 'destroy'])->name('destroy');
    });

    // Komponen CRUD
    Route::prefix('komponen')->name('komponen.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\KomponenController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Admin\KomponenController::class, 'store'])->name('store');
        Route::get('/create', [App\Http\Controllers\Admin\KomponenController::class, 'create'])->name('create');
        Route::get('/{komponen}/edit', [App\Http\Controllers\Admin\KomponenController::class, 'edit'])->name('edit');
        Route::put('/{komponen}', [App\Http\Controllers\Admin\KomponenController::class, 'update'])->name('update');
        Route::delete('/{komponen}', [App\Http\Controllers\Admin\KomponenController::class, 'destroy'])->name('destroy');
    });

    // Laporan Penjualan
    Route::prefix('laporan-penjualan')->name('laporan-penjualan.')->group(function () {
        Route::get('/', [LaporanPenjualanController::class, 'index'])->name('index');
        Route::get('/export', [LaporanPenjualanController::class, 'exportExcel'])->name('export');
        Route::get('/export-pdf', [LaporanPenjualanController::class, 'exportPDF'])->name('export-pdf');
        Route::get('/laporan-detail', [LaporanPenjualanController::class, 'detail'])->name('detail');
    });

    // Customer (Users)
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');

    // Order detail
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    });
    

    // AJAX detail laporan penjualan (untuk modal detail pada halaman laporan)
    Route::get('/laporan-penjualan/{id}/detail', [LaporanPenjualanController::class, 'detail'])
        ->name('laporan-penjualan.detail-json');

    // Installation service requests (admin)
    Route::get('/installations', [AdminInstallationRequestController::class, 'index'])->name('installations.index');
    Route::patch('/installations/{installation}/status', [AdminInstallationRequestController::class, 'updateStatus'])->name('installations.update-status');
});
       
