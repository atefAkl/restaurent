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
use App\Http\Controllers\UserController;
use App\Http\Controllers\CashierSessionController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PosDeviceController;
use App\Http\Controllers\PosStationController;
use App\Http\Controllers\PrinterController;
use App\Http\Controllers\PrinterSettingsController;
use App\Http\Controllers\ReportTemplateController;
use App\Http\Controllers\ReportThemeController;
use App\Http\Controllers\ReportComponentController;
use App\Http\Controllers\RoleController;

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
    Route::get('/index', [UserController::class, 'index'])->name('users.index');
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

// Cashier Sessions Routes
Route::prefix('cashier-sessions')->middleware('auth')->group(function () {
    Route::get('/', [CashierSessionController::class, 'index'])->name('cashier-sessions.index');
    Route::post('/', [CashierSessionController::class, 'store'])->name('cashier-sessions.store');
    Route::post('/{id}/end', [CashierSessionController::class, 'endSession'])->name('cashier-sessions.end');
    Route::get('/{id}', [CashierSessionController::class, 'show'])->name('cashier-sessions.show');
    Route::get('/{id}/print', [CashierSessionController::class, 'print'])->name('cashier-sessions.print');
});

// POS Devices Routes
Route::prefix('pos-devices')->middleware('auth')->group(function () {
    Route::get('/', [PosDeviceController::class, 'index'])->name('pos-devices.index');
    Route::post('/', [PosDeviceController::class, 'store'])->name('pos-devices.store');
    Route::put('/{id}', [PosDeviceController::class, 'update'])->name('pos-devices.update');
    Route::delete('/{id}', [PosDeviceController::class, 'destroy'])->name('pos-devices.destroy');
    Route::post('/{id}/test', [PosDeviceController::class, 'testConnection'])->name('pos-devices.test');
    Route::post('/{id}/print-test', [PosDeviceController::class, 'printTest'])->name('pos-devices.print-test');
    Route::get('/{id}', [PosDeviceController::class, 'show'])->name('pos-devices.show');
});

// Printers Routes
Route::prefix('printers')->group(function () {
    Route::get('/', [PrinterController::class, 'index'])->name('printers.index');
    Route::post('/', [PrinterController::class, 'store'])->name('printers.store');
    Route::put('/{id}', [PrinterController::class, 'update'])->name('printers.update');
    Route::delete('/{id}', [PrinterController::class, 'destroy'])->name('printers.destroy');
    Route::post('/{id}/test', [PrinterController::class, 'testConnection'])->name('printers.test');
    Route::post('/{id}/print-test', [PrinterController::class, 'printTest'])->name('printers.print-test');
    Route::post('/{id}/print', [PrinterController::class, 'printContent'])->name('printers.print');
    Route::get('/{id}', [PrinterController::class, 'show'])->name('printers.show');
});

// API Routes for AJAX calls
Route::prefix('api')->middleware('auth')->group(function () {
    // Get current shift info
    Route::get('/current-shift', [ShiftController::class, 'getCurrentShift']);

    // Printer Discovery API Routes
    Route::get('/printers/discover', [\App\Http\Controllers\Api\PrinterDiscoveryController::class, 'discover']);
    Route::post('/printers/test', [\App\Http\Controllers\Api\PrinterDiscoveryController::class, 'testPrinter']);
    Route::post('/printers/print-test', [\App\Http\Controllers\Api\PrinterDiscoveryController::class, 'printTestPage']);

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

// POS Devices management
Route::resource('pos-devices', App\Http\Controllers\PosDeviceController::class);

// API Routes for POS Devices
Route::prefix('api/pos-devices')->group(function () {
    Route::get('/discover', [App\Http\Controllers\Api\PosDeviceController::class, 'discover']);
    Route::post('/test-connection', [App\Http\Controllers\Api\PosDeviceController::class, 'testConnection']);
    Route::get('/{deviceId}/info', [App\Http\Controllers\Api\PosDeviceController::class, 'getDeviceInfo']);
    Route::post('/{deviceId}/send-command', [App\Http\Controllers\Api\PosDeviceController::class, 'sendCommand']);
    Route::get('/ports/common', [App\Http\Controllers\Api\PosDeviceController::class, 'getCommonPorts']);
});

// // Fallback Route
// Route::fallback(function () {
//     return response()->view('errors.404', [], 404);
// Printers resource routes
Route::resource('printers', PrinterController::class)->middleware('auth');
Route::post('/printers/test-connection', [PrinterController::class, 'testConnection'])->middleware('auth');
Route::post('/printers/{id}/print-test', [PrinterController::class, 'printTestPage'])->middleware('auth');

// Printer settings routes
Route::get('/printer-settings', [PrinterSettingsController::class, 'index'])->name('printer-settings.index')->middleware('auth');
Route::post('/printer-settings/update', [PrinterSettingsController::class, 'update'])->name('printer-settings.update')->middleware('auth');
Route::get('/printer-settings/get', [PrinterSettingsController::class, 'getSettings'])->name('printer-settings.get')->middleware('auth');
Route::post('/printer-settings/test-receipt', [PrinterSettingsController::class, 'testReceipt'])->name('printer-settings.test-receipt')->middleware('auth');

// Settings main page
Route::prefix('settings')->middleware('auth')->group(function () {
    Route::get('/index', function () {
        return view('settings.index');
    })->name('settings.index');

    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('pos-stations', PosStationController::class);

    // Report Components Management
    Route::resource('report-components', ReportComponentController::class);
    Route::get('/report-components/{reportComponent}/duplicate', [ReportComponentController::class, 'duplicate'])->name('report-components.duplicate');
    Route::post('/report-components/{reportComponent}/preview', [ReportComponentController::class, 'preview'])->name('report-components.preview');
    Route::get('/api/report-components/available', [ReportComponentController::class, 'availableComponents'])->name('report-components.available');

    // Report Templates Management
    Route::resource('report-templates', ReportTemplateController::class);
    Route::get('/report-templates/{reportTemplate}/duplicate', [ReportTemplateController::class, 'duplicate'])->name('report-templates.duplicate');
    Route::get('/report-templates/{reportTemplate}/preview', [ReportTemplateController::class, 'preview'])->name('report-templates.preview');
    Route::get('/report-templates/{reportTemplate}/customize', [ReportTemplateController::class, 'customize'])->name('report-templates.customize');
    Route::post('/report-templates/{reportTemplate}/save-customize', [ReportTemplateController::class, 'saveCustomize'])->name('report-templates.save-customize');
    Route::get('/report-templates/{reportTemplate}/blocks', [ReportTemplateController::class, 'blocks'])->name('report-templates.blocks');
    Route::post('/report-templates/{reportTemplate}/blocks', [ReportTemplateController::class, 'updateBlocks'])->name('report-templates.blocks.update');
    Route::post('/report-templates/import-a4', [ReportTemplateController::class, 'importA4'])->name('report-templates.import-a4');

    // Report Themes Management
    Route::resource('report-themes', ReportThemeController::class);
    Route::get('/report-themes/{reportTheme}/duplicate', [ReportThemeController::class, 'duplicate'])->name('report-themes.duplicate');
    Route::get('/report-themes/{reportTheme}/preview', [ReportThemeController::class, 'preview'])->name('report-themes.preview');
});

// Assign roles to users
Route::get('/users/{user}/roles', [UserController::class, 'editRoles'])->name('users.roles.edit')->middleware('auth');
Route::put('/users/{user}/roles', [UserController::class, 'updateRoles'])->name('users.roles.update')->middleware('auth');
