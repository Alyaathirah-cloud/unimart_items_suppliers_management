<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Mail\PurchaseOrderCreatedToSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * EmailNotificationController
 *
 * Handles manual "send email notification" button clicks from the owner UI.
 * Recipient emails are resolved from the database (supplier->contact_email).
 */
class EmailNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:owner']);
    }


}
