<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\PurchaseOrder;

class StaffController extends Controller
{
    public function dashboard()
    {
        $lowStockCount = Item::where('quantity', '>', 0)->whereColumn('quantity', '<=', 'reorder_point')->count();
        $pendingPOCount = PurchaseOrder::whereIn('status', ['Pending', 'Awaiting Supplier Approval'])->count();

        return view('staff.dashboard', compact('lowStockCount', 'pendingPOCount'));
    }
}
