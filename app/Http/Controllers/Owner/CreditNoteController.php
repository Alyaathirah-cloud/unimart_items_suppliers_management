<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CreditNote;
use Illuminate\Http\Request;

class CreditNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:owner']);
    }

    public function index(Request $request)
    {
        // Automatically expire unused credit notes older than 60 days
        \App\Models\CreditNote::where('status', 'Unused')
            ->whereRaw('DATE_ADD(issue_date, INTERVAL 60 DAY) < NOW()')
            ->update(['status' => 'Expired']);

        $query = CreditNote::with(['supplier', 'returnRequest.lines.item']);

        // Search by credit_note_id or supplier name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('credit_note_id', 'like', "%{$search}%")
                  ->orWhereHas('supplier', fn($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sort
        $sort = $request->get('sort', 'desc');
        $query->orderBy('issue_date', $sort === 'asc' ? 'asc' : 'desc');

        $creditNotes = $query->paginate(15)->appends($request->query());
        $suppliers   = \App\Models\Supplier::orderBy('name')->get();

        return view('owner.credit_notes.index', compact('creditNotes', 'suppliers'));
    }

    public function show(CreditNote $creditNote)
    {
        $creditNote->load(['supplier', 'returnRequest.lines.item']);
        return view('owner.credit_notes.show', compact('creditNote'));
    }

    public function exportPdf(Request $request)
    {
        $query = CreditNote::with(['supplier', 'returnRequest.lines.item']);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $creditNotes = $query->orderBy('issue_date', 'desc')->get();

        $filename = 'credit-notes-' . now()->format('Y-m-d') . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('owner.credit_notes.pdf', compact('creditNotes'))
            ->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    public function exportSinglePdf(CreditNote $creditNote)
    {
        $creditNote->load(['supplier', 'returnRequest.lines.item', 'purchaseOrder']);

        $filename = 'credit-note-' . $creditNote->credit_note_id . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('owner.credit_notes.single_pdf', compact('creditNote'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }
}
