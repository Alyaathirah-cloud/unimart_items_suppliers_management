@extends('layouts.staff')

@section('title', 'Add New Item – 22UniMart')

@push('styles')
<style>
    .content-center { display: flex; flex-direction: column; align-items: center; max-width: 800px; margin: 0 auto; width: 100%; }
    .page-title { font-size: 2rem; font-weight: 800; color: #0f2044; margin-bottom: 8px; text-align: center; }
    .page-sub { font-size: 0.95rem; color: #5a6a85; line-height: 1.6; margin-bottom: 32px; text-align: center; }
    .form-card { background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 1px 24px rgba(15,32,68,0.08); width: 100%; }
    .form-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; margin-bottom: 20px; }
    .form-group { display: flex; flex-direction: column; gap: 8px; }
    .form-label { font-size: 0.75rem; font-weight: 700; color: #5a6a85; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-control, .form-select { background: #f4f6fb; border: 1px solid transparent; border-radius: 10px; padding: 14px 16px; font-size: 0.95rem; color: #1a2744; transition: all 0.2s; outline: none; width: 100%; box-sizing: border-box; }
    .form-control:focus, .form-select:focus { background: #fff; border-color: #4a90d9; box-shadow: 0 0 0 3px rgba(74,144,217,0.12); }
    .form-control::placeholder { color: #9daec5; }
    .form-actions { display: flex; justify-content: center; gap: 16px; align-items: center; margin-top: 24px; }
    .btn-submit { background: #0f2044; color: #fff; border: none; border-radius: 10px; padding: 14px 26px; font-size: 0.95rem; font-weight: 700; cursor: pointer; transition: background 0.15s; }
    .btn-submit:hover { background: #122a50; }
    .btn-discard { color: #5a6a85; text-decoration: none; font-weight: 600; }
    .error-box { background: #fff3f3; border: 1px solid #f5c2c7; border-radius: 12px; padding: 18px; color: #842029; margin-bottom: 24px; width: 100%; }
</style>
@endpush

@section('breadcrumbs')
    <a href="{{ route('staff.inventory.index') }}">Inventory</a> › <span style="color:#0f2044;">Add Item</span>
@endsection

@section('content')
<div class="content-center">
    <div class="page-title">Add New Item</div>
    <div class="page-sub">Create a new product listing and keep stock details up to date.</div>

    @if($errors->any())
        <div class="error-box">
            <strong>There are errors with your submission.</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-card">
        <form action="{{ route('staff.inventory.store') }}" method="POST">
            @csrf

            <div class="form-row">
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label" for="name">Item Name <span style="color:#c0392b">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="category">Category</label>
                    <input type="text" id="category" name="category" class="form-control" value="{{ old('category') }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="unit_price">Unit Cost Price (RM)</label>
                    <input type="number" step="0.01" id="unit_price" name="unit_price" class="form-control" value="{{ old('unit_price') }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="markup_percentage">Markup Percentage</label>
                    <select id="markup_percentage" name="markup_percentage" class="form-select">
                        <option value="">-- Select Markup --</option>
                        <option value="20" @selected(old('markup_percentage') == '20')>+20% (Supplier Delivery Item)</option>
                        <option value="30" @selected(old('markup_percentage') == '30')>+30% (Cash & Carry / Shop Purchase Item)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="selling_price_display">Selling Price (RM)</label>
                    <input type="text" id="selling_price_display" class="form-control" style="background:#eef2f7;font-weight:600;color:#1d8348" readonly placeholder="Auto-calculated">
                </div>

                <div class="form-group">
                    <label class="form-label" for="quantity">Quantity <span style="color:#c0392b">*</span></label>
                    <input type="number" id="quantity" name="quantity" class="form-control" min="0" value="{{ old('quantity', 0) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="reorder_point">Reorder Point <span style="color:#c0392b">*</span></label>
                    <input type="number" id="reorder_point" name="reorder_point" class="form-control" min="0" value="{{ old('reorder_point', 0) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="expiry_date">Expiry Date</label>
                    <input type="date" id="expiry_date" name="expiry_date" class="form-control" value="{{ old('expiry_date') }}" min="{{ date('Y-m-d') }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="supplier_id">Supplier</label>
                    <select id="supplier_id" name="supplier_id" class="form-select">
                        <option value="">-- Select Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Add Item</button>
                <a href="{{ route('staff.inventory.index') }}" class="btn-discard">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const unitPriceInput = document.getElementById('unit_price');
        const markupSelect = document.getElementById('markup_percentage');
        const sellingPriceDisplay = document.getElementById('selling_price_display');

        function calculateSellingPrice() {
            const costPrice = parseFloat(unitPriceInput.value);
            const markup = parseFloat(markupSelect.value);
            if (!isNaN(costPrice) && !isNaN(markup)) {
                sellingPriceDisplay.value = 'RM ' + (costPrice + costPrice * markup / 100).toFixed(2);
            } else {
                sellingPriceDisplay.value = '';
            }
        }

        unitPriceInput.addEventListener('input', calculateSellingPrice);
        markupSelect.addEventListener('change', calculateSellingPrice);
        calculateSellingPrice();
    });
</script>
@endpush
