<?php

use App\Services\TelegramService;
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

Route::get('/debug-pocky', function () {
    $item = \App\Models\Item::where('name', 'Pocky Pistachio')->first();
    file_put_contents(public_path('debug_pocky.json'), json_encode($item));
    return 'ok';
});

Route::get('/debug-restore', function () {
    $item = \App\Models\Item::where('name', 'Pocky Pistachio')->first();
    if ($item) {
        $item->is_damaged = true;
        $item->damaged_quantity = 1;
        $item->save();
    }
    return 'restored';
});

Route::get('/', function () {
    // Server-side redirect for authenticated users — no JS hop, no /home hop
    $user = auth()->user() ?? auth('supplier')->user();

    if ($user) {
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return redirect('/admin/users');
        }
        if (method_exists($user, 'isOwner') && $user->isOwner()) {
            return redirect('/owner/dashboard');
        }
        if (method_exists($user, 'isStaff') && $user->isStaff()) {
            return redirect('/staff/dashboard');
        }
        if (method_exists($user, 'isSupplier') && $user->isSupplier()) {
            return redirect('/supplier/dashboard');
        }
    }

    return view('welcome');
});

// Keep backward compatibility: /home used by some auth scaffolding. Redirect to role dashboards.
Route::get('/home', function (\Illuminate\Http\Request $request) {
    $user = auth()->user() ?? auth('supplier')->user();
    \Illuminate\Support\Facades\Log::info('Hit /home route. User: ' . ($user ? $user->email . ' (Role: ' . $user->role . ')' : 'none'));
    if (! $user) {
        return redirect('/');
    }
    if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
        return redirect('/admin/users');
    }
    if (method_exists($user, 'isOwner') && $user->isOwner()) {
        return redirect('/owner/dashboard');
    }
    if (method_exists($user, 'isStaff') && $user->isStaff()) {
        return redirect('/staff/dashboard');
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

// Staff Login
Route::get('staff/login', [App\Http\Controllers\Auth\StaffLoginController::class, 'showLoginForm'])->name('staff.login')->middleware('guest');
Route::post('staff/login', [App\Http\Controllers\Auth\StaffLoginController::class, 'login'])->middleware('guest');
Route::post('staff/logout', [App\Http\Controllers\Auth\StaffLoginController::class, 'logout'])->name('staff.logout')->middleware('auth');

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
        return redirect('/owner/dashboard');
    } elseif ($user->isStaff()) {
        return redirect('/staff/dashboard');
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

    Route::middleware('role.owner')->group(function () {
        Route::get('staff', [App\Http\Controllers\Owner\StaffController::class, 'index'])->name('staff.index');
        Route::get('staff/create', [App\Http\Controllers\Owner\StaffController::class, 'create'])->name('staff.create');
        Route::post('staff', [App\Http\Controllers\Owner\StaffController::class, 'store'])->name('staff.store');
        Route::delete('staff/{user}', [App\Http\Controllers\Owner\StaffController::class, 'destroy'])->name('staff.destroy');

        Route::get('staff/{user}/edit', [App\Http\Controllers\Owner\StaffController::class, 'edit'])->name('staff.edit');
        Route::put('staff/{user}', [App\Http\Controllers\Owner\StaffController::class, 'update'])->name('staff.update');
        Route::patch('staff/{user}/toggle-status', [App\Http\Controllers\Owner\StaffController::class, 'toggleStatus'])->name('staff.toggle-status');
        Route::patch('staff/{user}/approve', [App\Http\Controllers\Owner\StaffController::class, 'approve'])->name('staff.approve');
        Route::patch('staff/{user}/reject', [App\Http\Controllers\Owner\StaffController::class, 'reject'])->name('staff.reject');
        Route::get('staff/{user}/reset-password', [App\Http\Controllers\Owner\StaffController::class, 'resetPasswordShow'])->name('staff.reset-password');
        Route::put('staff/{user}/reset-password', [App\Http\Controllers\Owner\StaffController::class, 'resetPasswordUpdate'])->name('staff.reset-password.update');

        Route::get('suppliers/export-pdf', [App\Http\Controllers\Owner\SupplierController::class, 'exportPdf'])->name('suppliers.export-pdf');
        Route::resource('suppliers', App\Http\Controllers\Owner\SupplierController::class);
        Route::post('suppliers/{supplier}/resend-email', [App\Http\Controllers\Owner\SupplierController::class, 'resendEmailInvite'])->name('suppliers.resend-email');
        Route::post('suppliers/{supplier}/resend-whatsapp', [App\Http\Controllers\Owner\SupplierController::class, 'resendWhatsAppInvite'])->name('suppliers.resend-whatsapp');
        Route::get('suppliers/{supplier}/credit-notes', [App\Http\Controllers\PurchaseOrderController::class, 'getSupplierCreditNotes'])->name('suppliers.credit-notes');
    });

    Route::get('items/{item}/export-pdf', [App\Http\Controllers\ItemController::class, 'exportDetailsPdf'])->name('items.export-details');
    Route::get('items/export', [App\Http\Controllers\ItemController::class, 'exportPdf'])->name('items.export');
    Route::patch('items/{item}/mark-expired', [App\Http\Controllers\ItemController::class, 'markExpired'])->name('items.markExpired');
    Route::patch('items/{item}/mark-damaged', [App\Http\Controllers\ItemController::class, 'markDamaged'])->name('items.markDamaged');
    Route::patch('items/{item}/unmark-damaged', [App\Http\Controllers\ItemController::class, 'unmarkDamaged'])->name('items.unmarkDamaged');
    Route::resource('items', App\Http\Controllers\ItemController::class);
    Route::get('purchase-orders/generate-missing-invoices', [App\Http\Controllers\PurchaseOrderController::class, 'generateMissingInvoices'])->name('purchase-orders.generate-missing-invoices');
    Route::resource('purchase-orders', App\Http\Controllers\PurchaseOrderController::class)->only(['index','create','store','show','edit','update']);
    Route::patch('purchase-orders/{purchase_order}/mark-received', [App\Http\Controllers\PurchaseOrderController::class, 'markAsReceived'])->name('purchase-orders.markAsReceived');
    Route::get('purchase-orders/{purchase_order}/invoice-status', [App\Http\Controllers\PurchaseOrderController::class, 'invoiceStatus'])->name('purchase-orders.invoice-status');
    Route::get('purchase-orders/export-csv', [App\Http\Controllers\PurchaseOrderController::class, 'exportCsv'])->name('purchase-orders.export-csv');
    Route::get('purchase-orders/generate', [App\Http\Controllers\PurchaseOrderController::class, 'generateForLowStock']);
    Route::get('purchase-orders/low-stock-items/{supplier}', [App\Http\Controllers\PurchaseOrderController::class, 'getLowStockItemsBySupplier'])->name('purchase-orders.low-stock-items');
    Route::post('purchase-orders/{purchase_order}/approve', [App\Http\Controllers\PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve');
    Route::post('purchase-orders/{purchase_order}/reject',  [App\Http\Controllers\PurchaseOrderController::class, 'reject'])->name('purchase-orders.reject');
    Route::get('return-requests/invoice-items/{invoice}', [App\Http\Controllers\ReturnRequestController::class, 'getInvoiceItems'])->name('return-requests.invoice-items');
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
    Route::post('invoices/{invoice}/save-estimates', [App\Http\Controllers\Owner\InvoiceController::class, 'saveEstimates'])->name('invoices.save-estimates');
    Route::resource('direct-purchases', App\Http\Controllers\DirectPurchaseController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::get('notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/mark-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAll');
    Route::patch('notifications/{notification}', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    // ── WhatsApp Notification routes (manual trigger via AJAX buttons) ────────
    Route::post('whatsapp/notify-low-stock',                    [App\Http\Controllers\WhatsAppNotificationController::class, 'notifyLowStock'])->name('whatsapp.notify-low-stock');
    Route::post('whatsapp/notify-expiry',                       [App\Http\Controllers\WhatsAppNotificationController::class, 'notifyExpiry'])->name('whatsapp.notify-expiry');
    Route::post('whatsapp/notify-rr-approved/{returnRequest}',  [App\Http\Controllers\WhatsAppNotificationController::class, 'notifyReturnRequestApproved'])->name('whatsapp.notify-rr-approved');
    Route::post('whatsapp/invite-supplier/{supplier}',          [App\Http\Controllers\WhatsAppNotificationController::class, 'inviteSupplier'])->name('whatsapp.invite-supplier');
    Route::post('whatsapp/notify-return-request/{returnRequest}',[App\Http\Controllers\WhatsAppNotificationController::class, 'notifyReturnRequest'])->name('whatsapp.notify-return-request');

    // ── Email Notification routes (manual trigger via AJAX buttons) ─────────
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
        Route::get('purchase-orders/{order}', [App\Http\Controllers\SupplierController::class, 'showOrder'])->name('purchase-orders.show');
        Route::post('purchase-orders/{order}/confirm', [App\Http\Controllers\SupplierController::class, 'confirmOrder'])->name('orders.confirm');
        Route::post('purchase-orders/{order}/delivery', [App\Http\Controllers\SupplierController::class, 'setDelivery'])->name('orders.delivery');
        Route::get('return-requests', [App\Http\Controllers\SupplierController::class, 'returnRequests'])->name('returns.index');
        Route::post('return-requests/{return}/status', [App\Http\Controllers\SupplierController::class, 'updateReturnStatus'])->name('returns.status');
        Route::get('return-requests/{return}/credit-note', [App\Http\Controllers\SupplierController::class, 'getCreditNote'])->name('returns.credit-note');
        Route::get('credit-notes', [App\Http\Controllers\SupplierController::class, 'creditNotes'])->name('credit-notes.index');
        Route::get('credit-notes/{creditNote}', [App\Http\Controllers\SupplierController::class, 'creditNoteShow'])->name('credit-notes.show');
        Route::get('credit-notes/{creditNote}/export-pdf', [App\Http\Controllers\SupplierController::class, 'exportSinglePdf'])->name('credit-notes.export-single-pdf');
        Route::get('invoices', [App\Http\Controllers\SupplierController::class, 'invoices'])->name('invoices.index');
        Route::get('invoices/{invoice}', [App\Http\Controllers\SupplierController::class, 'showInvoice'])->name('invoices.show');
        Route::post('invoices/{invoice}/mark-paid', [App\Http\Controllers\SupplierController::class, 'markInvoicePaid'])->name('invoices.markPaid');
        Route::get('invoices/{invoice}/export-pdf', [App\Http\Controllers\SupplierController::class, 'exportInvoicePdf'])->name('invoices.export-pdf');
        Route::get('notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/mark-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAll');
        Route::patch('notifications/{notification}', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    });
});

Route::get('/telegram-test', function () {
    $telegram = new TelegramService();
    $telegram->send('Hello from Laravel');
    return 'Message Sent';
});

// Product Return Request routes
Route::post('/product-rr/{id}/approve', [App\Http\Controllers\ProductReturnRequestController::class, 'approve'])->name('product-rr.approve');
Route::post('/product-rr/{id}/reject', [App\Http\Controllers\ProductReturnRequestController::class, 'reject'])->name('product-rr.reject');

Route::get('/test-telegram', function () {
    $telegram = new TelegramService();
    $telegram->send("Test message for this chat ID");
    return "Sent!";
});

// Staff section
Route::prefix('staff')->name('staff.')->middleware(['auth', 'staff'])->group(function () {
    // Dashboard
    Route::get('dashboard', [App\Http\Controllers\Staff\StaffController::class, 'dashboard'])->name('dashboard');

    // Inventory
    Route::resource('inventory', App\Http\Controllers\Staff\StaffInventoryController::class)->except(['destroy']);

    // Purchase Orders
    Route::get('purchase-orders/export-csv', [App\Http\Controllers\Staff\StaffPurchaseOrderController::class, 'exportCsv'])->name('po.export-csv');
    Route::resource('purchase-orders', App\Http\Controllers\Staff\StaffPurchaseOrderController::class)->except(['destroy'])->names([
        'index' => 'po.index',
        'create' => 'po.create',
        'store' => 'po.store',
        'show' => 'po.show',
        'edit' => 'po.edit',
        'update' => 'po.update',
    ]);

    // Return Requests
    Route::resource('return-requests', App\Http\Controllers\Staff\StaffReturnRequestController::class)->only(['index', 'show'])->names([
        'index' => 'rr.index',
        'show' => 'rr.show',
    ]);

    // Invoices
    Route::get('invoices/{invoice}/export-pdf', [App\Http\Controllers\Staff\StaffInvoiceController::class, 'exportPdf'])->name('invoice.export-pdf');
    Route::resource('invoices', App\Http\Controllers\Staff\StaffInvoiceController::class)->only(['index', 'show'])->names([
        'index' => 'invoice.index',
        'show' => 'invoice.show',
    ]);

    // Credit Notes
    Route::resource('credit-notes', App\Http\Controllers\Staff\StaffCreditNoteController::class)->only(['index', 'show'])->names([
        'index' => 'cn.index',
        'show' => 'cn.show',
    ]);

    // Profile & Password
    Route::get('profile', [App\Http\Controllers\Staff\StaffProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [App\Http\Controllers\Staff\StaffProfileController::class, 'update'])->name('profile.update');
    Route::get('password/change', [App\Http\Controllers\Staff\StaffProfileController::class, 'changePasswordShow'])->name('password.change');
    Route::put('password', [App\Http\Controllers\Staff\StaffProfileController::class, 'changePasswordUpdate'])->name('password.update');

    // Notifications
    Route::get('notifications', [App\Http\Controllers\Staff\StaffNotificationController::class, 'index'])->name('notif.index');
});
