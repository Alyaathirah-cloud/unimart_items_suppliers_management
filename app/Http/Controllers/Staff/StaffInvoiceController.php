<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class StaffInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('supplier');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', fn($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(15);
        $suppliers = \App\Models\Supplier::orderBy('name')->get();

        return view('staff.invoices.index', compact('invoices', 'suppliers'));
    }

    public function show($id)
    {
        $invoice = Invoice::with(['supplier', 'lines.item', 'purchaseOrder', 'creditNotes'])->findOrFail($id);
        return view('staff.invoices.show', compact('invoice'));
    }

    public function exportPdf(Invoice $invoice)
    {
        $invoice->load(['lines.item', 'supplier', 'purchaseOrder.item']);

        $filename = 'invoice-' . $invoice->invoice_number . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('owner.invoices.pdf', compact('invoice'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }
}
