<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Http\Request;


class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:owner']);
    }

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
                case 'damaged':
                    $query->where('is_damaged', true);
                    break;
                case 'ok':
                    $query->whereColumn('quantity', '>', 'reorder_point')
                        ->where(function ($q) use ($warningDays) {
                            $q->whereNull('expiry_date')
                              ->orWhereDate('expiry_date', '>', now()->addDays($warningDays));
                        });
                    break;
            }
        }

        $items = $query
            ->orderByRaw("CASE
                WHEN expiry_date IS NOT NULL AND DATE(expiry_date) < CURDATE() THEN 0
                WHEN quantity <= reorder_point THEN 1
                WHEN expiry_date IS NOT NULL AND DATE(expiry_date) <= DATE_ADD(CURDATE(), INTERVAL ? DAY) AND DATE(expiry_date) >= CURDATE() THEN 2
                ELSE 3
            END", [$warningDays])
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $outOfStock = Item::where('quantity', 0)->count();
        $lowStock   = Item::where('quantity', '>', 0)->whereColumn('quantity', '<=', 'reorder_point')->count();
        $nearExpiry = Item::whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays($warningDays))
            ->whereDate('expiry_date', '>=', now())
            ->count();
        $expired    = Item::whereNotNull('expiry_date')->whereDate('expiry_date', '<', now())->count();
        $categories = Item::select('category')->distinct()->whereNotNull('category')->pluck('category');

        // Collect item IDs that have a pending return request line
        $pendingReturnItemIds = \App\Models\ReturnRequestLine::whereHas(
            'returnRequest', fn($q) => $q->where('status', 'Pending')
        )->pluck('item_id')->unique()->values();

        return view('owner.items.index', compact(
            'items', 'outOfStock', 'lowStock', 'nearExpiry', 'expired',
            'categories', 'keyword', 'category', 'status',
            'pendingReturnItemIds'
        ));
    }

    public function create()
    {
        $suppliers = \App\Models\Supplier::all();
        return view('owner.items.create', compact('suppliers'));
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

        // Check if name is too similar to an existing name
        $existingNames = Item::pluck('name');
        foreach ($existingNames as $existing) {
            if (strtolower(trim($request->name)) !== strtolower(trim($existing)) && levenshtein(strtolower(trim($request->name)), strtolower(trim($existing))) <= 2) {
                return back()->withErrors(['name' => 'Item name is too similar to an existing item: ' . $existing])->withInput();
            }
        }

        // Auto-correct category if similar one exists (singular/plural/case)
        if ($request->category) {
            $inputCategory = trim($request->category);
            $normalizedInput = Str::singular(strtolower($inputCategory));
            
            $existingCategories = Item::whereNotNull('category')->distinct()->pluck('category');
            
            foreach ($existingCategories as $existing) {
                $normalizedExisting = Str::singular(strtolower($existing));
                if ($normalizedInput === $normalizedExisting) {
                    $request->merge(['category' => $existing]);
                    break;
                }
            }
        }

        $data = $request->all();
        
        // Calculate selling price based on unit cost and markup
        if (isset($data['unit_price']) && isset($data['markup_percentage'])) {
            $unitPrice = (float) $data['unit_price'];
            $markup = (float) $data['markup_percentage'];
            $data['selling_price'] = $unitPrice + ($unitPrice * $markup / 100);
        }

        Item::create($data);
        return redirect()->route('owner.items.index')->with('success', 'Item added successfully!');
    }

    public function show(Item $item)
    {
        return view('owner.items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $suppliers = \App\Models\Supplier::all();
        return view('owner.items.edit', compact('item', 'suppliers'));
    }

    public function update(Request $request, Item $item)
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

        // Check if name is too similar to another existing name
        $existingNames = Item::where('id', '!=', $item->id)->pluck('name');
        foreach ($existingNames as $existing) {
            if (strtolower(trim($request->name)) !== strtolower(trim($existing)) && levenshtein(strtolower(trim($request->name)), strtolower(trim($existing))) <= 2) {
                return back()->withErrors(['name' => 'Item name is too similar to an existing item: ' . $existing])->withInput();
            }
        }

        // Auto-correct category if similar one exists (singular/plural/case)
        if ($request->category) {
            $inputCategory = trim($request->category);
            $normalizedInput = Str::singular(strtolower($inputCategory));
            
            $existingCategories = Item::whereNotNull('category')->distinct()->pluck('category');
            
            foreach ($existingCategories as $existing) {
                $normalizedExisting = Str::singular(strtolower($existing));
                if ($normalizedInput === $normalizedExisting) {
                    $request->merge(['category' => $existing]);
                    break;
                }
            }
        }

        $data = $request->all();
        
        // Calculate selling price based on unit cost and markup
        if (isset($data['unit_price']) && isset($data['markup_percentage'])) {
            $unitPrice = (float) $data['unit_price'];
            $markup = (float) $data['markup_percentage'];
            $data['selling_price'] = $unitPrice + ($unitPrice * $markup / 100);
        }

        $item->update($data);

        // Auto-recalculate selling price on save (server-side authoritative recalc)
        if ($request->has('unit_price')) {
            $markup = $item->markup_percentage ?? 20;
            $item->selling_price = round($item->unit_price * (1 + $markup / 100), 2);
            $item->save();
        }

        return redirect()->route('owner.items.index')->with('success', 'Item updated successfully!');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('owner.items.index')->with('success', 'Item deleted successfully!');
    }

    /**
     * Mark a near-expiry item as expired immediately.
     */
    public function markExpired(Item $item)
    {
        $item->update(['expiry_date' => now()->subDay()]);
        return redirect()->route('owner.dashboard')->with('success', "{$item->name} has been marked as expired.");
    }

    /**
     * Mark an item as damaged with a quantity and reason.
     */
    public function markDamaged(Request $request, Item $item)
    {
        $maxDamagedQuantity = max(0, (int) $item->quantity);

        $request->validate([
            'damage_reason'   => 'required|string|max:255',
            'damaged_quantity' => "required|integer|min:1|max:{$maxDamagedQuantity}",
        ]);

        $damagedQuantity = (int) $request->input('damaged_quantity');

        $item->update([
            'is_damaged'       => true,
            'damage_reason'    => $request->damage_reason,
            'damaged_quantity' => $damagedQuantity,
        ]);

        $unitLabel = $damagedQuantity === 1 ? 'unit' : 'units';

        return back()->with('success', "{$item->name} has been flagged as damaged ({$damagedQuantity} {$unitLabel}) for: {$request->damage_reason}.");
    }

    /**
     * Clear the damaged flag from an item.
     */
    public function unmarkDamaged(Item $item)
    {
        $item->update([
            'is_damaged'       => false,
            'damage_reason'    => null,
            'damaged_quantity' => 0,
        ]);

        return back()->with('success', "Damaged flag removed from {$item->name}.");
    }

    public function exportPdf(Request $request)
    {
        $keyword = $request->input('keyword');
        $category = $request->input('category');
        $status = $request->input('status');
        $warningDays = Setting::get('expiry_warning_days', 7);

        $query = Item::with('supplier');

        if ($keyword) {
            $query->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('category', 'like', "%{$keyword}%")
                    ->orWhereHas('supplier', function ($supplierQuery) use ($keyword) {
                        $supplierQuery->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($status) {
            switch ($status) {
                case 'low_stock':
                    $query->whereColumn('quantity', '<=', 'reorder_point');
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
                case 'ok':
                    $query->whereColumn('quantity', '>', 'reorder_point')
                        ->where(function ($query) use ($warningDays) {
                            $query->whereNull('expiry_date')
                                ->orWhereDate('expiry_date', '>', now()->addDays($warningDays));
                        });
                    break;
            }
        }

        $items = $query->orderBy('name')->get();

        $filename = 'inventory-list-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($items) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Category', 'Quantity', 'Unit Price (RM)', 'Reorder Point', 'Expiry Date', 'Supplier', 'Status']);
            foreach ($items as $item) {
                $status = $item->isExpired() ? 'Expired'
                        : ($item->isNearExpiry() ? 'Near Expiry'
                        : ($item->isLowStock()  ? 'Low Stock' : 'Normal'));
                fputcsv($handle, [
                    $item->name,
                    $item->category ?? '',
                    $item->quantity,
                    number_format($item->unit_price, 2),
                    $item->reorder_point,
                    $item->expiry_date ? $item->expiry_date->format('Y-m-d') : '',
                    optional($item->supplier)->name ?? '',
                    $status,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportDetailsPdf(Item $item)
    {
        $item->load('supplier');
        $status = $item->isExpired() ? 'Expired'
                : ($item->isNearExpiry() ? 'Near Expiry'
                : ($item->isLowStock()  ? 'Low Stock' : 'Normal'));

        $filename = 'item-' . $item->id . '-' . Str::slug($item->name) . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($item, $status) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Field', 'Value']);
            fputcsv($handle, ['Name',          $item->name]);
            fputcsv($handle, ['Category',      $item->category ?? '']);
            fputcsv($handle, ['Quantity',      $item->quantity]);
            fputcsv($handle, ['Unit Price (RM)', number_format($item->unit_price, 2)]);
            fputcsv($handle, ['Reorder Point', $item->reorder_point]);
            fputcsv($handle, ['Expiry Date',   $item->expiry_date ? $item->expiry_date->format('Y-m-d') : '']);
            fputcsv($handle, ['Supplier',      optional($item->supplier)->name ?? '']);
            fputcsv($handle, ['Status',        $status]);
            fputcsv($handle, ['Created At',    $item->created_at->format('Y-m-d H:i:s')]);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
