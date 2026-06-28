@extends('layouts.staff')

@section('title', 'Edit Supplier – 22UniMart')

@push('styles')
<style>
    .content-center { display: flex; flex-direction: column; align-items: center; max-width: 900px; margin: 0 auto; width: 100%; }
    .page-title { font-size: 1.8rem; font-weight: 800; color: #0f2044; margin-bottom: 8px; text-align: center; }
    .page-sub { font-size: 0.9rem; color: #5a6a85; line-height: 1.5; margin-bottom: 32px; max-width: 600px; text-align: center; }

    .form-container { width: 100%; }
    .form-card { background: #fff; border-radius: 12px; padding: 32px; margin-bottom: 24px; box-shadow: 0 1px 15px rgba(15,32,68,0.03); width: 100%; }
    
    .card-header { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
    .card-line { width: 24px; height: 3px; background: #0f2044; border-radius: 2px; }
    .card-title { font-size: 1.05rem; font-weight: 700; color: #0f2044; }

    .form-row { display: flex; gap: 24px; margin-bottom: 20px; flex-wrap: wrap; }
    .form-group { flex: 1; min-width: 240px; display: flex; flex-direction: column; gap: 8px; }
    .form-label { font-size: 0.75rem; font-weight: 700; color: #5a6a85; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-control { background: #f4f6fb; border: 1px solid transparent; border-radius: 6px; padding: 12px 16px; font-size: 0.9rem; font-family: 'Inter', sans-serif; color: #1a2744; transition: all 0.2s; outline: none; }
    .form-control:focus { background: #fff; border-color: #4a90d9; box-shadow: 0 0 0 3px rgba(74, 144, 217, 0.1); }
    .form-control::placeholder { color: #9daec5; }
    .form-text { font-size: 0.75rem; color: #7a8fa8; margin-top: 4px; }

    .error-msg { font-size: 0.75rem; color: #c0392b; margin-top: 4px; font-weight: 500; }

    .form-actions { display: flex; justify-content: center; align-items: center; gap: 16px; margin-top: 20px; margin-bottom: 40px; }
    .btn-submit { background: #0f2044; color: #fff; border: none; border-radius: 6px; padding: 12px 24px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.15s; font-family: 'Inter', sans-serif; }
    .btn-submit:hover { background: #182e5e; }
    .btn-discard { background: #e2e8f0; color: #3a4d6a; border: none; border-radius: 6px; padding: 12px 24px; font-size: 0.9rem; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; transition: background 0.15s; }
    .btn-discard:hover { background: #cbd5e1; }

    /* Portal info rows */
    .info-grid { display: grid; gap: 12px; margin-bottom: 16px; }
    .info-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 16px; background: #f4f6fb; border-radius: 10px; }
    .info-label { font-size: 0.88rem; font-weight: 700; color: #0f2044; }
    .info-value { font-size: 0.88rem; color: #334155; word-break: break-all; }
    .copy-btn { border: 1px solid #e2e8f0; background: #fff; color: #0f2044; border-radius: 8px; padding: 7px 12px; cursor: pointer; font-size: 0.78rem; font-family: 'Inter', sans-serif; transition: background 0.15s; }
    .copy-btn:hover { background: #f1f5f9; }
    .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 5px 10px; border-radius: 999px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .status-pill.sent { background: #d1fae5; color: #065f46; }
    .status-pill.pending { background: #fef3c7; color: #92400e; }
    .status-pill.failed { background: #fee2e2; color: #991b1b; }
    .status-pill.missing { background: #e2e8f0; color: #334155; }
    .status-pill.disabled { background: #e2e8f0; color: #64748b; }
    
    .flash { padding: 13px 18px; border-radius: 8px; font-size: 0.88rem; margin-bottom: 20px; font-weight: 500; display: flex; align-items: center; gap: 10px; }
    .flash-success { background: #e8f8f0; color: #1d8348; border: 1px solid #a9dfbf; }
    .flash-error   { background: #fdedec; color: #c0392b; border: 1px solid #f5b7b1; }
    .flash-warning { background: #fff8e1; color: #856404; border: 1px solid #ffc107; }
</style>
@endpush

@section('breadcrumbs')
    <a href="{{ route('staff.suppliers.index') }}">Suppliers</a> › <span style="color:#0f2044;">Edit Supplier</span>
@endsection

@section('content')
<div class="content-center">
    <div class="page-title">Edit Supplier</div>
    <div class="page-sub">Update contact information for <strong>{{ $supplier->name }}</strong>. Changes will be reflected immediately across all linked records.</div>

    @if(session('success'))
        <div class="flash flash-success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash flash-error">❌ {{ session('error') }}</div>
    @endif
    @if(session('warning'))
        <div class="flash flash-warning">⚠️ {!! nl2br(e(session('warning'))) !!}</div>
    @endif

    <form action="{{ route('staff.suppliers.update', $supplier) }}" method="POST" class="form-container">
        @csrf
        @method('PUT')

        {{-- Basic Information --}}
        <div class="form-card">
            <div class="card-header">
                <div class="card-line"></div>
                <div class="card-title">Basic Information</div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Company Name</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Global Tech Logistics"
                           value="{{ old('name', $supplier->name) }}" required>
                    @error('name')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="contact_person">Contact Person</label>
                    <input type="text" id="contact_person" name="contact_person" class="form-control" placeholder="Jane Supplier"
                           value="{{ old('contact_person', $supplier->contact_person) }}">
                    @error('contact_person')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="contact_email">Email Address</label>
                    <input type="email" id="contact_email" name="contact_email" class="form-control" placeholder="corporate@supplier.com"
                           value="{{ old('contact_email', $supplier->contact_email) }}" required>
                    @error('contact_email')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="contact_phone">Phone Number</label>
                    <input type="text" id="contact_phone" name="contact_phone" class="form-control" placeholder="+60123456789"
                           value="{{ old('contact_phone', $supplier->contact_phone) }}">
                    @error('contact_phone')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Address Information --}}
        <div class="form-card">
            <div class="card-header">
                <div class="card-line"></div>
                <div class="card-title">Address Information</div>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex:1;">
                    <label class="form-label" for="address_line_1">Address Line 1</label>
                    <input type="text" id="address_line_1" name="address_line_1" class="form-control" placeholder="123 Supply Street"
                           value="{{ old('address_line_1', $supplier->address_line_1) }}">
                    @error('address_line_1')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex:1;">
                    <label class="form-label" for="address_line_2">Address Line 2 <span style="font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
                    <input type="text" id="address_line_2" name="address_line_2" class="form-control" placeholder="Suite 9, Floor 2"
                           value="{{ old('address_line_2', $supplier->address_line_2) }}">
                    @error('address_line_2')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="city">City</label>
                    <input type="text" id="city" name="city" class="form-control" placeholder="Kuala Lumpur"
                           value="{{ old('city', $supplier->city) }}">
                    @error('city')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="state">State</label>
                    <input type="text" id="state" name="state" class="form-control" placeholder="WP Kuala Lumpur"
                           value="{{ old('state', $supplier->state) }}">
                    @error('state')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="postal_code">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" class="form-control" placeholder="50000"
                           value="{{ old('postal_code', $supplier->postal_code) }}">
                    @error('postal_code')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="country">Country</label>
                    <input type="text" id="country" name="country" class="form-control" placeholder="Malaysia"
                           value="{{ old('country', $supplier->country) }}">
                    @error('country')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Portal Access --}}
        <div class="form-card">
            <div class="card-header">
                <div class="card-line"></div>
                <div class="card-title">Portal Access</div>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex:1;">
                    <label class="form-label" for="portal_enabled">Portal Status</label>
                    <div style="display:flex;align-items:center;gap:10px;background:#f4f6fb;border-radius:8px;padding:14px 16px;">
                        <input type="checkbox" id="portal_enabled" name="portal_enabled" value="1"
                               {{ old('portal_enabled', $supplier->portal_enabled) ? 'checked' : '' }}>
                        <div>
                            <div style="font-size:0.9rem;font-weight:600;color:#0f2044;">Enable supplier portal access</div>
                            <div class="form-text">Supplier can log in and manage orders and returns.</div>
                        </div>
                    </div>
                    @error('portal_enabled')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>

            <div id="portal-password-section" class="form-row">
                <div class="form-group">
                    <label class="form-label" for="password">Reset Password</label>
                    <input type="password" id="password" name="password" class="form-control"
                           placeholder="Leave blank to keep current password" autocomplete="new-password">
                    <div class="form-text">Set a new password to reset portal access. Leave blank to keep the existing password.</div>
                    @error('password')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                           placeholder="Re-type new password" autocomplete="new-password">
                </div>
            </div>
        </div>

        <!-- Supplier Policy Card -->
        <div class="form-card">
            <div class="card-header">
                <div class="card-line"></div>
                <div class="card-title">Supplier Policy</div>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex:1;">
                    <label class="form-label" for="accepts_returns">Accepts Returns</label>
                    <div style="display:flex;align-items:center;gap:10px;background:#f4f6fb;border-radius:8px;padding:14px 16px;">
                        <input type="hidden" name="accepts_returns" value="0">
                        <input type="checkbox" id="accepts_returns" name="accepts_returns" value="1"
                               {{ old('accepts_returns', $supplier->accepts_returns) ? 'checked' : '' }}>
                        <div>
                            <div style="font-size:0.9rem;font-weight:600;color:#0f2044;">Supplier accepts return of expired or damaged stock</div>
                            <div class="form-text">When enabled, the Return Request page will recommend "Return to Supplier". When disabled, it will recommend "Dispose".</div>
                        </div>
                    </div>
                    @error('accepts_returns')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="form-actions">
            <button type="submit" class="btn-submit">Update Supplier</button>
            <a href="{{ route('staff.suppliers.index') }}" class="btn-discard">Cancel</a>
        </div>
    </form>

    {{-- Portal Access Overview (read-only) --}}
    @if($supplier->portal_enabled && $supplier->user)
        <div class="form-card">
            <div class="card-header">
                <div class="card-line"></div>
                <div class="card-title">Portal Access Overview</div>
            </div>

            <div class="info-grid">
                <div class="info-row">
                    <div>
                        <div class="info-label">Portal Login Link</div>
                        <div class="info-value">
                            <a href="{{ $supplier->portal_link ?? route('supplier.login') }}" target="_blank"
                               style="color:#0f2044;text-decoration:none;">
                                {{ $supplier->portal_link ?? route('supplier.login') }}
                            </a>
                        </div>
                    </div>
                    <button type="button" class="copy-btn" data-copy-text="{{ $supplier->portal_link ?? route('supplier.login') }}">Copy link</button>
                </div>

                <div class="info-row">
                    <div>
                        <div class="info-label">Portal Email</div>
                        <div class="info-value">{{ $supplier->contact_email }}</div>
                    </div>
                    <button type="button" class="copy-btn" data-copy-text="{{ $supplier->contact_email }}">Copy email</button>
                </div>

                <div class="info-row">
                    <div style="flex:1;">
                        <div class="info-label">Temporary Password</div>
                        @if($supplier->temporary_password)
                            <input id="temporary-password-value" type="password" class="form-control"
                                   readonly value="{{ $supplier->temporary_password }}"
                                   style="padding:10px 12px;background:#fff;margin-top:6px;" />
                        @else
                            <div class="info-value">No temporary password available</div>
                        @endif
                    </div>
                    @if($supplier->temporary_password)
                        <div style="display:flex;gap:10px;align-items:center;">
                            <button type="button" class="copy-btn" data-copy-text="{{ $supplier->temporary_password }}">Copy password</button>
                            <button type="button" id="toggle-password-visibility" class="copy-btn" data-visible="false">Show</button>
                        </div>
                    @endif
                </div>
            </div>

            <div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
                <span class="status-pill {{ $supplier->invite_email_status ?? 'pending' }}">
                    Email Invite: {{ ucfirst($supplier->invite_email_status ?? 'pending') }}
                </span>
            </div>

            <div style="font-size:0.88rem;color:#5a6a85;margin-bottom:16px;">
                Resend portal access invitations to the supplier. A new temporary password will be generated after each resend.
            </div>

            <div style="display:flex;gap:14px;flex-wrap:wrap;">
                <form action="{{ route('staff.suppliers.resend-email', $supplier) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-discard" style="color:#0f2044;border-color:#e2e8f0;background:#fff;">✉️ Resend Email Invite</button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const portalToggle = document.getElementById('portal_enabled');
        const portalPasswordSection = document.getElementById('portal-password-section');

        if (portalToggle && portalPasswordSection) {
            const sync = () => {
                portalPasswordSection.style.display = portalToggle.checked ? 'flex' : 'none';
            };
            portalToggle.addEventListener('change', sync);
            sync();
        }

        // Copy buttons
        document.querySelectorAll('[data-copy-text]').forEach(function(btn) {
            btn.dataset.originalText = btn.textContent;
            btn.addEventListener('click', function() {
                const val = this.getAttribute('data-copy-text') || '';
                if (!val.trim()) return;
                navigator.clipboard.writeText(val).then(() => {
                    this.textContent = 'Copied!';
                    setTimeout(() => { this.textContent = this.dataset.originalText; }, 1500);
                });
            });
        });

        // Toggle temp password visibility
        const toggleBtn = document.getElementById('toggle-password-visibility');
        const pwInput   = document.getElementById('temporary-password-value');
        if (toggleBtn && pwInput) {
            toggleBtn.addEventListener('click', function() {
                const vis = this.dataset.visible === 'true';
                pwInput.type = vis ? 'password' : 'text';
                this.dataset.visible = vis ? 'false' : 'true';
                this.textContent = vis ? 'Show' : 'Hide';
            });
        }
    });
</script>
@endpush
