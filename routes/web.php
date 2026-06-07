<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

// Keep backward compatibility: /home used by some auth scaffolding. Redirect to role dashboards.
Route::get('/home', function () {
    $user = auth()->user() ?? auth('supplier')->user();
    if (! $user) {
        return redirect('/');
    }
    if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
        return redirect('/admin/users');
    }
    if (method_exists($user, 'isOwner') && $user->isOwner()) {
        return redirect('/owner/dashboard');
    }
    if (method_exists($user, 'isSupplier') && $user->isSupplier()) {
        Auth::guard('web')->logout();
        Auth::guard('supplier')->login($user);
        session()->regenerate();
        return redirect('/supplier/dashboard');
    }
    return redirect('/');
})->name('home')->middleware('auth:web,supplier');
// authentication routes (if using Laravel Breeze/Jetstream, register accordingly)
Auth::routes();
Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth:web,supplier');

Route::middleware(['auth:web,supplier'])->group(function () {
    Route::get('password/force-change', [App\Http\Controllers\Auth\ForcePasswordChangeController::class, 'show'])->name('password.force-change');
    Route::post('password/force-change', [App\Http\Controllers\Auth\ForcePasswordChangeController::class, 'update'])->name('password.force-change.update');
});

// General dashboard route (fallback)
Route::get('/dashboard', function () {
    $user = auth()->user() ?? auth('supplier')->user();
    if (!$user) return redirect('/');
    
    if ($user->isAdmin()) {
        return redirect('/admin/users');
    } elseif ($user->isOwner()) {
        return redirect('/owner/items');
    } elseif ($user->isSupplier()) {
        Auth::guard('web')->logout();
        Auth::guard('supplier')->login($user);
        session()->regenerate();
        return redirect('/supplier/dashboard');
    }
    return redirect('/');
})->middleware('auth:web,supplier')->name('dashboard');

// Admin section
Route::prefix('admin')->name('admin.')->middleware(['auth','role:admin'])->group(function () {
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::get('settings/edit', [App\Http\Controllers\Admin\SettingController::class, 'edit'])->name('settings.edit');
    Route::post('settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/test-whatsapp', [App\Http\Controllers\Admin\SettingController::class, 'testWhatsApp'])->name('settings.test-whatsapp');
    // other report routes
});

// Owner section
Route::prefix('owner')->name('owner.')->middleware(['auth','role:owner'])->group(function () {
    // Profile and account
    Route::get('profile', [App\Http\Controllers\Owner\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [App\Http\Controllers\Owner\ProfileController::class, 'update'])->name('profile.update');
    Route::get('password/change', [App\Http\Controllers\Owner\ProfileController::class, 'changePasswordShow'])->name('password.change');
    Route::put('password', [App\Http\Controllers\Owner\ProfileController::class, 'changePasswordUpdate'])->name('password.update');

    Route::get('dashboard', function () {
        return view('owner.dashboard');
    })->name('dashboard');
    Route::get('suppliers/export-pdf', [App\Http\Controllers\Owner\SupplierController::class, 'exportPdf'])->name('suppliers.export-pdf');
    Route::resource('suppliers', App\Http\Controllers\Owner\SupplierController::class);
    Route::post('suppliers/{supplier}/resend-email', [App\Http\Controllers\Owner\SupplierController::class, 'resendEmailInvite'])->name('suppliers.resend-email');
    Route::post('suppliers/{supplier}/resend-whatsapp', [App\Http\Controllers\Owner\SupplierController::class, 'resendWhatsAppInvite'])->name('suppliers.resend-whatsapp');
    Route::get('suppliers/{supplier}/credit-notes', [App\Http\Controllers\PurchaseOrderController::class, 'getSupplierCreditNotes'])->name('suppliers.credit-notes');
    Route::get('items/{item}/export-pdf', [App\Http\Controllers\ItemController::class, 'exportDetailsPdf'])->name('items.export-details');
    Route::get('items/export', [App\Http\Controllers\ItemController::class, 'exportPdf'])->name('items.export');
    Route::patch('items/{item}/mark-expired', [App\Http\Controllers\ItemController::class, 'markExpired'])->name('items.markExpired');
    Route::patch('items/{item}/mark-damaged', [App\Http\Controllers\ItemController::class, 'markDamaged'])->name('items.markDamaged');
    Route::patch('items/{item}/unmark-damaged', [App\Http\Controllers\ItemController::class, 'unmarkDamaged'])->name('items.unmarkDamaged');
    Route::resource('items', App\Http\Controllers\ItemController::class);
    Route::resource('purchase-orders', App\Http\Controllers\PurchaseOrderController::class)->only(['index','create','store','show','edit','update']);
    Route::patch('purchase-orders/{purchase_order}/mark-received', [App\Http\Controllers\PurchaseOrderController::class, 'markAsReceived'])->name('purchase-orders.markAsReceived');
    Route::get('purchase-orders/{purchase_order}/invoice-status', [App\Http\Controllers\PurchaseOrderController::class, 'invoiceStatus'])->name('purchase-orders.invoice-status');
    Route::get('purchase-orders/export-csv', [App\Http\Controllers\PurchaseOrderController::class, 'exportCsv'])->name('purchase-orders.export-csv');
    Route::get('purchase-orders/generate', [App\Http\Controllers\PurchaseOrderController::class, 'generateForLowStock']);
    Route::get('purchase-orders/low-stock-items/{supplier}', [App\Http\Controllers\PurchaseOrderController::class, 'getLowStockItemsBySupplier'])->name('purchase-orders.low-stock-items');
    Route::resource('return-requests', App\Http\Controllers\ReturnRequestController::class)->only(['index','create','store','show','destroy']);
    Route::patch('return-requests/{returnRequest}/submit', [App\Http\Controllers\ReturnRequestController::class, 'submit'])->name('return-requests.submit');
    Route::get('return-requests/export-pdf', [App\Http\Controllers\ReturnRequestController::class, 'exportPdf'])->name('return-requests.export-pdf');
    Route::get('return-requests/{returnRequest}/credit-note', [App\Http\Controllers\ReturnRequestController::class, 'getCreditNote'])->name('return-requests.credit-note');
    Route::get('credit-notes', [App\Http\Controllers\Owner\CreditNoteController::class, 'index'])->name('credit-notes.index');
    Route::get('credit-notes/export-pdf', [App\Http\Controllers\Owner\CreditNoteController::class, 'exportPdf'])->name('credit-notes.export-pdf');
    Route::get('credit-notes/{creditNote}', [App\Http\Controllers\Owner\CreditNoteController::class, 'show'])->name('credit-notes.show');
    Route::get('credit-notes/{creditNote}/export-pdf', [App\Http\Controllers\Owner\CreditNoteController::class, 'exportSinglePdf'])->name('credit-notes.export-single-pdf');
    Route::resource('invoices', App\Http\Controllers\Owner\InvoiceController::class)->only(['index','create','store','show','destroy']);
    Route::get('invoices/{invoice}/export-pdf', [App\Http\Controllers\Owner\InvoiceController::class, 'exportPdf'])->name('invoices.export-pdf');
    Route::patch('invoices/{invoice}/mark-paid', [App\Http\Controllers\Owner\InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
    Route::resource('direct-purchases', App\Http\Controllers\DirectPurchaseController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::get('notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/mark-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAll');
    Route::patch('notifications/{notification}', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    // reports routes
});

// Supplier login and section
Route::prefix('supplier')->name('supplier.')->group(function () {
    Route::get('login', [App\Http\Controllers\Auth\SupplierLoginController::class, 'showLoginForm'])
        ->name('login')
        ->middleware('guest:supplier');

    Route::post('login', [App\Http\Controllers\Auth\SupplierLoginController::class, 'login'])
        ->middleware('guest:supplier');

    Route::middleware(['auth:supplier','role:supplier'])->group(function () {
        Route::get('dashboard', [App\Http\Controllers\SupplierController::class, 'dashboard'])->name('dashboard');
        Route::get('purchase-orders', [App\Http\Controllers\SupplierController::class, 'orders'])->name('purchase-orders.index');
        Route::post('purchase-orders/{order}/confirm', [App\Http\Controllers\SupplierController::class, 'confirmOrder'])->name('orders.confirm');
        Route::post('purchase-orders/{order}/delivery', [App\Http\Controllers\SupplierController::class, 'setDelivery'])->name('orders.delivery');
        Route::get('return-requests', [App\Http\Controllers\SupplierController::class, 'returnRequests'])->name('returns.index');
        Route::post('return-requests/{return}/status', [App\Http\Controllers\SupplierController::class, 'updateReturnStatus'])->name('returns.status');
        Route::get('return-requests/{return}/credit-note', [App\Http\Controllers\SupplierController::class, 'getCreditNote'])->name('returns.credit-note');
        Route::get('credit-notes', [App\Http\Controllers\SupplierController::class, 'creditNotes'])->name('credit-notes.index');
        Route::get('credit-notes/{creditNote}', [App\Http\Controllers\SupplierController::class, 'creditNoteShow'])->name('credit-notes.show');
        Route::get('credit-notes/{creditNote}/export-pdf', [App\Http\Controllers\SupplierController::class, 'exportSinglePdf'])->name('credit-notes.export-single-pdf');
        Route::get('invoices', [App\Http\Controllers\SupplierController::class, 'invoices'])->name('invoices.index');
        Route::get('invoices/{invoice}', [App\Http\Controllers\SupplierController::class, 'invoiceShow'])->name('invoices.show');
        Route::get('invoices/{invoice}/export-pdf', [App\Http\Controllers\SupplierController::class, 'exportInvoicePdf'])->name('invoices.export-pdf');
        Route::get('notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/mark-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAll');
        Route::patch('notifications/{notification}', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    });
});
