<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\CreditNote;
use Illuminate\Http\Request;

class StaffCreditNoteController extends Controller
{
    public function index(Request $request)
    {
        // Automatically expire unused credit notes older than 60 days
        \App\Models\CreditNote::where('status', 'Unused')
            ->whereRaw('DATE_ADD(issue_date, INTERVAL 60 DAY) < NOW()')
            ->update(['status' => 'Expired']);

        $query = CreditNote::with('supplier');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $creditNotes = $query->orderBy('created_at', 'desc')->paginate(15);
        $suppliers = \App\Models\Supplier::orderBy('name')->get();
        return view('staff.credit_notes.index', compact('creditNotes', 'suppliers'));
    }

    public function show($id)
    {
        $creditNote = CreditNote::with(['supplier', 'returnRequest.lines.item', 'invoice'])->findOrFail($id);
        return view('staff.credit_notes.show', compact('creditNote'));
    }
}
