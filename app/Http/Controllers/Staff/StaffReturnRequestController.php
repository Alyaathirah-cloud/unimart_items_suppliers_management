<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;

class StaffReturnRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ReturnRequest::with(['supplier', 'lines.item']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('return_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', fn($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        $query->orderBy('created_at', 'desc');

        $requests = $query->paginate(15);
        $suppliers = \App\Models\Supplier::orderBy('name')->get();

        return view('staff.return_requests.index', compact('requests', 'suppliers'));
    }

    public function show($id)
    {
        $returnRequest = ReturnRequest::with(['lines.item', 'supplier', 'creditNote'])->findOrFail($id);
        return view('staff.return_requests.show', compact('returnRequest'));
    }
}
