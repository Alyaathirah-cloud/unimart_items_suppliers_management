<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductReturnRequest;
use App\Services\TelegramService;

class ProductReturnRequestController extends Controller
{
    public function approve($id)
    {
        $returnRequest = ProductReturnRequest::with('product')->findOrFail($id);

        if ($returnRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending requests can be approved.');
        }

        $returnRequest->update(['status' => 'approved']);

        $product = $returnRequest->product;
        $product->increment('stock', $returnRequest->quantity);



        return redirect()->back()->with('success', 'Return request approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $returnRequest = ProductReturnRequest::with('product')->findOrFail($id);

        if ($returnRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending requests can be rejected.');
        }

        $reason = $request->input('admin_note', 'No reason provided');
        
        $returnRequest->update([
            'status' => 'rejected',
            'admin_note' => $reason
        ]);

        $product = $returnRequest->product;



        return redirect()->back()->with('success', 'Return request rejected.');
    }
}
