<?php

namespace App\Http\Controllers;

use App\Models\DirectPurchase;
use App\Models\DirectPurchaseLine;
use App\Models\Item;
use Illuminate\Http\Request;

class DirectPurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:owner']);
    }

    public function index(Request $request)
    {
        $query = DirectPurchase::with('lines.item');

        if ($request->filled('store_name')) {
            $query->where('store_name', 'like', '%' . $request->store_name . '%');
        }

        $purchases = $query->orderBy('purchase_date', 'desc')->paginate(15);
        return view('owner.direct_purchases.index', compact('purchases'));
    }

    public function create()
    {
        $items = Item::where('source_type', 'direct')->orderBy('name')->get();
        return view('owner.direct_purchases.create', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'notes' => 'nullable|string',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.quantity' => 'required|integer|min:1',
            'lines.*.uom' => 'nullable|string|max:50',
            'lines.*.amount_paid' => 'required|numeric|min:0.01',
        ]);

        $directPurchase = DirectPurchase::create([
            'store_name' => $request->store_name,
            'purchase_date' => $request->purchase_date,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
            'total_amount' => 0,
        ]);

        $totalAmount = 0;

        if ($request->has('lines')) {
            foreach ($request->lines as $lineData) {
                if (!isset($lineData['item_id'])) continue;

                $item = Item::find($lineData['item_id']);
                $quantity = (int)$lineData['quantity'];
                $amountPaid = (float)$lineData['amount_paid'];
                $uom = $lineData['uom'] ?? 'unit';

                // Calculate unit_price: amount_paid ÷ quantity
                $unitPrice = $amountPaid / max(1, $quantity);

                // Calculate selling_price: unit_price × 1.30 (30% markup)
                $sellingPrice = $unitPrice * 1.30;

                $directPurchaseLine = DirectPurchaseLine::create([
                    'direct_purchase_id' => $directPurchase->id,
                    'item_id' => $item->id,
                    'quantity' => $quantity,
                    'uom' => $uom,
                    'amount_paid' => $amountPaid,
                    'unit_price' => $unitPrice,
                    'selling_price' => $sellingPrice,
                ]);

                // Update item quantity and prices
                $item->quantity += $quantity;
                $item->unit_price = $unitPrice;
                $item->selling_price = $sellingPrice;
                $item->save();

                $totalAmount += $amountPaid;
            }
        }

        $directPurchase->total_amount = $totalAmount;
        $directPurchase->save();

        return redirect()->route('owner.direct-purchases.show', $directPurchase)
            ->with('success', 'Direct purchase recorded successfully. Stock and prices updated.');
    }

    public function show(DirectPurchase $directPurchase)
    {
        $directPurchase->load(['lines.item', 'createdBy']);
        return view('owner.direct_purchases.show', compact('directPurchase'));
    }

    public function destroy(DirectPurchase $directPurchase)
    {
        // Reverse the stock and price updates
        foreach ($directPurchase->lines as $line) {
            $item = $line->item;
            if ($item) {
                $item->quantity -= $line->quantity;
                $item->save();
            }
        }

        $directPurchase->lines()->delete();
        $directPurchase->delete();

        return redirect()->route('owner.direct-purchases.index')
            ->with('success', 'Direct purchase deleted successfully.');
    }
}
