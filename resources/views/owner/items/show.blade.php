@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="mb-3">
        <a href="{{ route('owner.items.index') }}" class="btn btn-success">📦 Inventory</a>
        <a href="{{ route('owner.suppliers.index') }}" class="btn btn-info">🏢 Suppliers</a>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <h1>Item Details</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('owner.items.export-details', $item) }}" class="btn btn-info">📄 Export to CSV</a>
            <a href="{{ route('owner.items.edit', $item) }}" class="btn btn-primary">Edit Item</a>
            <a href="{{ route('owner.items.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ $item->name }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Name:</dt>
                        <dd class="col-sm-8">{{ $item->name }}</dd>

                        <dt class="col-sm-4">Category:</dt>
                        <dd class="col-sm-8">{{ $item->category ?? '-' }}</dd>

                        <dt class="col-sm-4">Quantity:</dt>
                        <dd class="col-sm-8">{{ $item->quantity }}</dd>

                        <dt class="col-sm-4">Reorder Point:</dt>
                        <dd class="col-sm-8">{{ $item->reorder_point }}</dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Expiry Date:</dt>
                        <dd class="col-sm-8">{{ $item->expiry_date ? $item->expiry_date->format('Y-m-d') : '-' }}</dd>

                        <dt class="col-sm-4">Supplier:</dt>
                        <dd class="col-sm-8">{{ optional($item->supplier)->name ?? '-' }}</dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-{{ $item->statusClass() }}">{{ $item->statusLabel() }}</span>
                        </dd>

                        <dt class="col-sm-4">Created:</dt>
                        <dd class="col-sm-8">{{ $item->created_at->format('Y-m-d H:i') }}</dd>
                    </dl>
                </div>
            </div>

            @if($item->expiry_date && $item->expiry_date < now())
                <div class="alert alert-danger mt-3">
                    <strong>⚠️ This item has expired!</strong> Expiry date: {{ $item->expiry_date->format('Y-m-d') }}
                </div>
                <a href="{{ route('owner.return-requests.create', ['item_id' => $item->id]) }}" class="btn btn-warning mt-3">Create Return Request</a>
            @elseif($item->expiry_date && $item->expiry_date <= now()->addDays(7))
                <div class="alert alert-warning mt-3">
                    <strong>⚠️ This item is expiring soon!</strong> Expiry date: {{ $item->expiry_date->format('Y-m-d') }}
                </div>
            @endif

            @if($item->quantity <= $item->reorder_point)
                <div class="alert alert-warning mt-3">
                    <strong>⚠️ Low stock alert!</strong> Current quantity ({{ $item->quantity }}) is at or below reorder point ({{ $item->reorder_point }}).
                </div>
            @endif
        </div>
    </div>
</div>
@endsection