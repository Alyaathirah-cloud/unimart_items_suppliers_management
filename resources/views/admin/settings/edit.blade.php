@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">System Settings</div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Low Stock Threshold</label>
                            <input type="number" name="low_stock_threshold" class="form-control @error('low_stock_threshold') is-invalid @enderror" value="{{ old('low_stock_threshold', $lowStockThreshold) }}" min="0">
                            @error('low_stock_threshold') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">Alert when quantity is below this value for items without a specific reorder point.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Expiry Alert Days</label>
                            <input type="number" name="expiry_warning_days" class="form-control @error('expiry_warning_days') is-invalid @enderror" value="{{ old('expiry_warning_days', $expiry) }}" min="0">
                            @error('expiry_warning_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Owner WhatsApp Number</label>
                            <input type="text" name="owner_whatsapp_number" class="form-control @error('owner_whatsapp_number') is-invalid @enderror" value="{{ old('owner_whatsapp_number', $ownerWhatsApp) }}">
                            @error('owner_whatsapp_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="hidden" name="whatsapp_enabled" value="0">
                            <input type="checkbox" name="whatsapp_enabled" id="whatsapp_enabled" class="form-check-input" value="1" {{ old('whatsapp_enabled', $whatsappEnabled) ? 'checked' : '' }}>
                            <label for="whatsapp_enabled" class="form-check-label">Enable WhatsApp Notifications</label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">CallMeBot Phone</label>
                            <input type="text" name="callmebot_phone" class="form-control @error('callmebot_phone') is-invalid @enderror" value="{{ old('callmebot_phone', $callMeBotPhone) }}">
                            @error('callmebot_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">Use the number returned by CallMeBot after allowing messages.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">CallMeBot API Key</label>
                            <input type="text" name="callmebot_api_key" class="form-control @error('callmebot_api_key') is-invalid @enderror" value="{{ old('callmebot_api_key', $callMeBotApiKey) }}">
                            @error('callmebot_api_key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </form>

                    <hr>

                    <form method="POST" action="{{ route('admin.settings.test-whatsapp') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-success">Test WhatsApp Notification</button>
                    </form>

                    <div style="margin-top:12px;font-size:0.9rem;color:#374151">
                        <div><strong>Last test:</strong> {{ $lastWhatsappTest ?? 'Never' }}</div>
                        <div><strong>Connection status:</strong> {{ $whatsappConnectionStatus ?? 'Unknown' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
