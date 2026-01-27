<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\OrderItemController;

// Language Switch
Route::get('locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Home Route
Route::get('/', function () {
    return redirect()->route('login');
});


// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');
Route::get('/dashboard/sales-data', [DashboardController::class, 'getSalesData'])->name('dashboard.sales-data')->middleware('auth');

// Orders Routes
Route::resource('orders', OrderController::class)->middleware('auth');
Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus')->middleware('auth');
Route::get('/orders/{order}/print', [OrderController::class, 'print'])->name('orders.print')->middleware('auth');
Route::get('/orders/kitchen', [OrderController::class, 'kitchenOrders'])->name('orders.kitchen')->middleware('auth');
Route::post('/orders/items/store', [OrderItemController::class, 'store'])->name('orders.items.store')->middleware('auth');
Route::put('/orders/items/{item}/update', [OrderItemController::class, 'update'])->name('orders.items.update')->middleware('auth');
Route::get('/orders/items/{item}/destroy', [OrderItemController::class, 'destroy'])->name('orders.items.destroy')->middleware('auth');


Route::group(['middleware' => 'auth'], function () {
    Route::get('/users', [UserController::class, 'addItems'])->name('orders.add-items');
});

// Clients Routes
Route::resource('clients', ClientController::class)->middleware('auth');
Route::get('/clients/search/by/name/or/phone', [ClientController::class, 'searchByNameOrPhone'])->name('clients.searchByNameOrPhone')->middleware('auth');
// Products Routes
Route::group(['middleware' => 'auth'], function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}/show', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::get('/products/{product}/copy', [ProductController::class, 'copy'])->name('products.copy');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}/update', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}/destroy', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggleStatus');
    Route::get('/products/by-category/{categoryId}', [ProductController::class, 'getProductsByCategory'])->name('products.by-category');
    Route::get('/products/low-stock', [ProductController::class, 'lowStock'])->name('products.low-stock');
});

// Categories Routes

// Settings: Rooms & Tables
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TableController;

Route::middleware('auth')->prefix('settings')->group(function () {
    Route::resource('rooms', RoomController::class);
    Route::resource('tables', TableController::class);
});
Route::resource('categories', CategoryController::class)->middleware('auth');
Route::post('/categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggleStatus')->middleware('auth');
Route::get('/categories/active', [CategoryController::class, 'getActiveCategories'])->name('categories.active')->middleware('auth');

// Shifts Routes
Route::resource('shifts', ShiftController::class)->middleware('auth');
Route::post('/shifts/{shift}/close', [ShiftController::class, 'close'])->name('shifts.close')->middleware('auth');
Route::get('/shifts/current', [ShiftController::class, 'getCurrentShift'])->name('shifts.current')->middleware('auth');
Route::post('/shifts/open', [ShiftController::class, 'openMyShift'])->name('shifts.open')->middleware('auth');
Route::post('/shifts/close', [ShiftController::class, 'closeMyShift'])->name('shifts.close-my')->middleware('auth');
Route::get('/shifts/{shift}/report', [ShiftController::class, 'getShiftReport'])->name('shifts.report')->middleware('auth');

// Expenses Routes
Route::resource('expenses', ExpenseController::class)->middleware('auth');
Route::get('/expenses/summary', [ExpenseController::class, 'getExpensesSummary'])->name('expenses.summary')->middleware('auth');
Route::get('/expenses/export/pdf', [ExpenseController::class, 'exportPdf'])->name('expenses.export.pdf')->middleware('auth');

// Reports Routes
Route::prefix('reports')->middleware('auth')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/sales', [ReportController::class, 'salesReport'])->name('reports.sales');
    Route::get('/products', [ReportController::class, 'productsReport'])->name('reports.products');
    Route::get('/expenses', [ReportController::class, 'expensesReport'])->name('reports.expenses');
    Route::get('/profit-loss', [ReportController::class, 'profitLossReport'])->name('reports.profit-loss');
    Route::get('/shifts', [ReportController::class, 'shiftsReport'])->name('reports.shifts');
});

// API Routes for AJAX calls
Route::prefix('api')->middleware('auth')->group(function () {
    // Get current shift info
    Route::get('/current-shift', [ShiftController::class, 'getCurrentShift']);

    // POS API Routes
    Route::get('/categories', [CategoryController::class, 'getActiveCategories']);
    Route::get('/categories/{categoryId}/products', [ProductController::class, 'getProductsByCategory']);
    Route::get('/customers/search', function (\Illuminate\Http\Request $request) {
        $customer = \App\Models\Customer::where('phone', $request->phone)->first();
        return response()->json($customer);
    });

    // Get products by category
    Route::get('/products/category/{categoryId}', [ProductController::class, 'getProductsByCategory']);

    // Get active categories
    Route::get('/categories/active', [CategoryController::class, 'getActiveCategories']);

    // Check product stock
    Route::get('/products/{product}/stock', function ($productId) {
        $product = \App\Models\Product::find($productId);
        return response()->json([
            'stock' => $product->stock_quantity,
            'low_stock' => $product->isLowStock()
        ]);
    });

    // Validate coupon
    Route::post('/coupons/validate', function (\Illuminate\Http\Request $request) {
        $coupon = \App\Models\Coupon::where('code', $request->code)->first();
        $orderAmount = $request->order_amount;

        if (!$coupon || !$coupon->isValid($orderAmount)) {
            return response()->json(['valid' => false, 'message' => 'الكوبون غير صالح']);
        }

        $discount = $coupon->calculateDiscount($orderAmount);

        return response()->json([
            'valid' => true,
            'discount' => $discount,
            'message' => 'تم تطبيق الخصم بنجاح'
        ]);
    });
});

// // Fallback Route
// Route::fallback(function () {
//     return response()->view('errors.404', [], 404);
// });

// Users resource routes
Route::resource('users', UserController::class)->middleware('auth');
