<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\Auth\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\UserAdmin;
use App\Models\Order;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

// 🔹 Home Page
// Route::get('/', function () {
//     return view('index');
// })->name('index');

// 🔹 Other Pages
Route::get('/product', [ProductController::class, 'index'])->name('product');
Route::view('/contact', 'contact')->name('contact');
Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('/search', [ProductController::class, 'search'])->name('product.search');

Route::middleware('auth')->group(function () {
    // My Account Dashboard
    Route::get('/my-account', [AccountController::class, 'index'])->name('account.index');

    // Profile Settings Routes
    Route::get('/my-account/profile-settings', [AccountController::class, 'profileSettings'])->name('account.profileSettings');
    // Address Management Routes
    Route::get('/my-account/addresses', [AccountController::class, 'addresses'])->name('account.addresses');
    Route::get('/my-account/addresses/create', [AccountController::class, 'createAddress'])->name('addresses.create');
    Route::get('/my-account/addresses/{id}/edit', [AccountController::class, 'editAddress'])->name('addresses.edit');
    Route::put('/my-account/addresses/{id}', [AccountController::class, 'updateAddress'])->name('addresses.update');
    Route::delete('/my-account/addresses/{id}', [AccountController::class, 'destroyAddress'])->name('addresses.destroy');
    Route::put('/my-account/addresses/update-all', [AccountController::class, 'updateAllAddresses'])->name('addresses.updateAll');
    // Profile Update Route (PUT Request)
    Route::put('/my-account/profile-update', [AccountController::class, 'updateProfile'])
        ->name('profile.update');

    // Remove Profile Picture Route (POST Request)
    Route::post('/my-account/remove-profile-picture', [AccountController::class, 'removeProfilePicture'])
        ->name('profile.removePicture');
    Route::post('/my-account/addresses/storeOrUpdate', [AccountController::class, 'storeOrUpdateAddress'])->name('addresses.storeOrUpdate');
});

//  🔹 User Authentication
Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup.submit');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 🔹 Forgot Password Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// عرض صفحة إعادة تعيين كلمة المرور
Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', [
        'token' => $token,
        'email' => request()->query('email'), // pass the email as well
    ]);
})->name('password.reset');

// تنفيذ عملية إعادة تعيين كلمة المرور
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])
    ->name('password.update');

// 🔹 Cart Routes (Protected for Logged-in Users)
Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/order/place', [OrderController::class, 'placeOrder'])->name('order.place');
    Route::post('/orders/request-refund', [OrderController::class, 'requestRefund'])->name('orders.request-refund');
    Route::put('/orders/{id}/cancel', [OrderController::class, 'cancelOrder'])->name('orders.cancel');
});

// guest cart
Route::post('/cart/buy-now', [CartController::class, 'buyNow'])->name('cart.buy-now')->middleware(UserAdmin::class);

// 🔹 Admin Authentication
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'adminLogin'])->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'adminLogout'])->name('admin.logout');

    // 🔹 Protect Admin Routes
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        // 🔹 All Users Route
        Route::get('/all-users', [UserController::class, 'index'])->name('admin.all-users');
        Route::delete('/all-users/{id}', [UserController::class, 'destroy'])->name('admin.delete-user');

        // 🔹 Product Management
        Route::get('/add-product', function () {
            return view('admin.add-product');
        })->name('admin.add-product');
        Route::post('/add-product', [ProductController::class, 'store'])->name('admin.add-product.store');

        Route::get('/view-products', [AdminController::class, 'viewProducts'])->name('admin.view-products');
        Route::put('/products/{id}/update', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/all-products/{id}', [ProductController::class, 'destroy'])->name('admin.delete-product');
        Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');

        // Order Management Routes
        Route::get('/view-orders', [OrderController::class, 'viewOrders'])->name('admin.view-orders');

        Route::get('/pending-orders', [OrderController::class, 'pendingOrders'])->name('admin.pending-orders');

        Route::get('/completed-orders', [OrderController::class, 'completedOrders'])->name('admin.completed-orders');

        Route::get('/process-refunds', [OrderController::class, 'processRefunds'])->name('admin.process-refunds');

        Route::get('/analytics', [OrderController::class, 'viewSales'])->name('admin.analytics');

        Route::delete('/orders/{id}/delete', [OrderController::class, 'deleteOrder'])->name('admin.orders.delete');
        Route::get('/orders/{orderNo}/invoice', [OrderController::class, 'generateInvoice'])->name('admin.orders.invoice');
        Route::put('/orders/{id}/approve', [OrderController::class, 'approveOrder'])->name('admin.orders.approve');
        Route::put('/orders/{id}/process', [OrderController::class, 'processOrder'])->name('admin.orders.process');
        Route::put('/orders/{id}/reject', [OrderController::class, 'rejectOrder'])->name('admin.orders.reject');
        Route::put('/orders/{id}/complete', [OrderController::class, 'completeOrder'])->name('admin.orders.complete');
        Route::post('/orders/{id}/approve-refund', [OrderController::class, 'approveRefund'])->name('admin.orders.approve-refund');
        Route::post('/orders/{id}/deny-refund', [OrderController::class, 'denyRefund'])->name('admin.orders.deny-refund');
        Route::post('/restock-product', [ProductController::class, 'restock'])->name('admin.restock-product');

        Route::get('/profile', [AdminAuthController::class, 'profile'])->name('admin.profile');
        Route::post('/profile/update', [AdminAuthController::class, 'updateProfile'])->name('admin.profile.update');

        // 🔹 Search page
        Route::get('/search', [\App\Http\Controllers\Admin\GlobalSearchController::class, 'search'])
            ->name('admin.search');
    });
});

// 🔹 Contact Form Submission
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');