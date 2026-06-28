<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StaffInventoryController extends Controller
{
    public function index(Request $request)
    {
        $keyword    = $request->input('keyword');
        $category   = $request->input('category');
        $status     = $request->input('status');
        $warningDays = Setting::get('expiry_warning_days', 7);

        $query = Item::with('supplier');

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('category', 'like', "%{$keyword}%")
                  ->orWhereHas('supplier', fn($sq) => $sq->where('name', 'like', "%{$keyword}%"));
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($status) {
            switch ($status) {
                case 'out_of_stock':
                    $query->where('quantity', 0);
                    break;
                case 'low_stock':
                    $query->where('quantity', '>', 0)->whereColumn('quantity', '<=', 'reorder_point');
                    break;
                case 'near_expiry':
                    $query->whereNotNull('expiry_date')
                        ->whereDate('expiry_date', '<=', now()->addDays($warningDays))
                        ->whereDate('expiry_date', '>=', now());
                    break;
                case 'expired':
                    $query->whereNotNull('expiry_date')
                        ->whereDate('expiry_date', '<', now());
                    break;
            }
        }

        $items = $query->orderBy('name')->paginate(15)->withQueryString();

        // Stats for top cards
        $outOfStock = Item::where('quantity', 0)->count();
        $lowStock   = Item::where('quantity', '>', 0)->whereColumn('quantity', '<=', 'reorder_point')->count();
        $expired    = Item::whereNotNull('expiry_date')->whereDate('expiry_date', '<', now())->count();
        $nearExpiry = Item::whereNotNull('expiry_date')
                          ->whereDate('expiry_date', '<=', now()->addDays($warningDays))
                          ->whereDate('expiry_date', '>=', now())->count();

        // Categories for the filter dropdown
        $categories = Item::whereNotNull('category')->distinct()->orderBy('category')->pluck('category');

        // Items with pending returns (to show badge)
        $pendingReturnItemIds = \App\Models\ReturnRequestLine::whereHas('returnRequest', function($q) {
            $q->where('status', 'Pending');
        })->pluck('item_id');

        return view('staff.inventory.index', compact(
            'items', 'keyword', 'category', 'status',
            'outOfStock', 'lowStock', 'expired', 'nearExpiry',
            'categories', 'pendingReturnItemIds'
        ));
    }

    public function create()
    {
        $suppliers = \App\Models\Supplier::all();
        return view('staff.inventory.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date|after_or_equal:today',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'reorder_point' => 'required|integer|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'markup_percentage' => 'nullable|numeric|in:20,30',
        ], [
            'expiry_date.after_or_equal' => 'Expiry date cannot be in the past.'
        ]);

        $data = $request->all();
        
        if (isset($data['unit_price']) && isset($data['markup_percentage'])) {
            $unitPrice = (float) $data['unit_price'];
            $markup = (float) $data['markup_percentage'];
            $data['selling_price'] = $unitPrice + ($unitPrice * $markup / 100);
        }

        Item::create($data);
        return redirect()->route('staff.inventory.index')->with('success', 'Item added successfully!');
    }

    public function show($id)
    {
        $item = Item::findOrFail($id);
        return view('staff.inventory.show', compact('item'));
    }

    public function edit($id)
    {
        $item = Item::findOrFail($id);
        $suppliers = \App\Models\Supplier::all();
        return view('staff.inventory.edit', compact('item', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date|after_or_equal:today',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'reorder_point' => 'required|integer|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'markup_percentage' => 'nullable|numeric|in:20,30',
        ], [
            'expiry_date.after_or_equal' => 'Expiry date cannot be in the past.'
        ]);

        $data = $request->all();
        
        if (isset($data['unit_price']) && isset($data['markup_percentage'])) {
            $unitPrice = (float) $data['unit_price'];
            $markup = (float) $data['markup_percentage'];
            $data['selling_price'] = $unitPrice + ($unitPrice * $markup / 100);
        }

        $item->update($data);

        return redirect()->route('staff.inventory.index')->with('success', 'Item updated successfully!');
    }
}
