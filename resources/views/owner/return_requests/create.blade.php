@extends('layouts.owner')

@section('title', 'Create Return Request – 22UniMart')

@push('styles')
    <style>
        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 28px; }
        .page-title { font-size: 1.9rem; font-weight: 800; color: #0f2044; margin-bottom: 4px; }
        .page-sub { font-size: 0.9rem; color: #5a6a85; line-height: 1.6; }

        /* Cards */
        .card { background: #fff; border-radius: 14px; box-shadow: 0 1px 20px rgba(15,32,68,0.07); margin-bottom: 24px; overflow: hidden; border: 1px solid #e8edf4; }
        .card-header { padding: 20px 24px 16px; border-bottom: 1px solid #eef2f7; display: flex; align-items: center; justify-content: space-between; }
        .card-title { font-size: 1rem; font-weight: 800; color: #0f2044; display: flex; align-items: center; gap: 8px; }
        .card-body { padding: 24px; }

        /* Suggestion list */
        .suggestion-item { display: flex; align-items: flex-start; gap: 14px; padding: 16px; border: 1.5px solid #e2e8f0; border-radius: 10px; margin-bottom: 12px; background: #fafbfd; transition: border-color 0.2s, background 0.2s; }
        .suggestion-item.checked { border-color: #4a90d9; background: #f0f7ff; }
        .suggestion-item.checked-damaged { border-color: #e74c3c; background: #fff8f7; }
        .suggestion-checkbox { margin-top: 3px; width: 18px; height: 18px; cursor: pointer; accent-color: #4a90d9; flex-shrink: 0; }
        .suggestion-info { flex: 1; }
        .suggestion-name { font-weight: 700; color: #0f2044; font-size: 0.95rem; }
        .suggestion-meta { font-size: 0.78rem; color: #7a8fa8; margin-top: 3px; display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
        .suggestion-meta .dot { color: #d1dce8; }
        .badge-expired { background: #fef3e2; color: #d4870a; border-radius: 4px; padding: 2px 7px; font-size: 0.7rem; font-weight: 700; }
        .badge-damaged { background: #fdedec; color: #c0392b; border-radius: 4px; padding: 2px 7px; font-size: 0.7rem; font-weight: 700; }
        /* Recommendation badges */
        .badge-return { background: #d1fae5; color: #065f46; border-radius: 4px; padding: 2px 8px; font-size: 0.7rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; }
        .badge-dispose { background: #fff3cd; color: #92400e; border-radius: 4px; padding: 2px 8px; font-size: 0.7rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; }
        .suggestion-controls { display: flex; gap: 12px; margin-top: 10px; align-items: center; flex-wrap: wrap; }
        .ctrl-group { display: flex; flex-direction: column; gap: 4px; }
        .ctrl-label { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #9daec5; }
        .form-control { border: 1px solid #d1dce8; border-radius: 7px; padding: 7px 10px; font-size: 0.88rem; font-family: Inter, sans-serif; color: #1a2744; background: #fff; outline: none; transition: border-color 0.15s; }
        .form-control:focus { border-color: #4a90d9; box-shadow: 0 0 0 3px rgba(74,144,217,0.1); }
        .form-control:disabled { background: #f4f6fb; color: #9daec5; cursor: not-allowed; }
        .qty-input { width: 90px; }

        /* Counter bar */
        .counter-bar { display: flex; align-items: center; gap: 24px; padding: 14px 18px; background: #f0f7ff; border-radius: 8px; margin-top: 8px; border: 1px solid #cce0f5; }
        .counter-item { font-size: 0.82rem; color: #3a4d6a; }
        .counter-item strong { color: #0f2044; font-size: 1rem; }

        /* Empty suggestions */
        .no-suggestions { text-align: center; padding: 48px 24px; color: #9daec5; }
        .no-suggestions .icon { font-size: 2.5rem; margin-bottom: 12px; }
        .no-suggestions p { font-size: 0.9rem; font-weight: 600; color: #7a8fa8; }
        .no-suggestions span { font-size: 0.8rem; color: #aab8cc; }

        /* Preview section */
        .preview-card { background: #fff; border-radius: 14px; box-shadow: 0 1px 20px rgba(15,32,68,0.07); margin-bottom: 24px; overflow: hidden; border: 1px solid #e8edf4; display: none; }
        .preview-card.visible { display: block; }
        .preview-warning { background: #fffbea; border-left: 3px solid #f59e0b; padding: 12px 18px; font-size: 0.82rem; color: #7a4d06; font-weight: 500; margin: 0 24px 16px; border-radius: 0 6px 6px 0; }
        .preview-group { padding: 0 24px 16px; }
        .preview-supplier { font-weight: 700; color: #0f2044; font-size: 0.88rem; margin-bottom: 4px; }
        .preview-invoice { font-size: 0.78rem; color: #7a8fa8; margin-bottom: 8px; }
        .preview-item { font-size: 0.85rem; color: #3a4d6a; padding: 4px 0; border-bottom: 1px solid #f4f6fb; display: flex; justify-content: space-between; }
        .preview-item:last-child { border-bottom: none; }
        .preview-separator { height: 1px; background: #eef2f7; margin: 12px 24px; }

        /* Notes */
        .notes-area { width: 100%; border: 1px solid #d1dce8; border-radius: 8px; padding: 12px 16px; font-size: 0.9rem; font-family: Inter, sans-serif; color: #1a2744; resize: vertical; min-height: 80px; outline: none; transition: border-color 0.15s; }
        .notes-area:focus { border-color: #4a90d9; box-shadow: 0 0 0 3px rgba(74,144,217,0.1); }

        /* Actions */
        .action-row { display: flex; align-items: center; gap: 16px; margin-top: 8px; }
        .btn-submit { background: #0f2044; color: #fff; border: none; border-radius: 10px; padding: 14px 32px; font-size: 0.95rem; font-weight: 700; cursor: pointer; transition: background 0.15s, transform 0.1s; font-family: Inter, sans-serif; }
        .btn-submit:hover { background: #1e3a6e; }
        .btn-submit:active { transform: translateY(1px); }
        .btn-submit:disabled { background: #9daec5; cursor: not-allowed; }
        .btn-cancel { background: #fff; border: 1px solid #d1dce8; color: #5a6a85; border-radius: 10px; padding: 13px 24px; font-size: 0.95rem; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; font-family: Inter, sans-serif; transition: background 0.15s; }
        .btn-cancel:hover { background: #f4f6fb; }
        .btn-back { background: #fff; border: 1px solid #e2e8f0; color: #3a4d6a; border-radius: 8px; padding: 10px 16px; font-size: 0.85rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.15s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-back:hover { background: #f8fafc; border-color: #d1dce8; }

        /* Error */
        .error-box { background: #fdedec; border: 1px solid #f5b7b1; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; color: #c0392b; font-size: 0.88rem; }
        .error-box ul { margin: 8px 0 0 16px; }
        
        .center-form { max-width: 1000px; margin: 0 auto; width: 100%; }
    </style>
@endpush

@section('breadcrumbs')
    <a href="{{ route('owner.return-requests.index') }}">Return Requests</a> › <span style="color:#0f2044;">Create</span>
@endsection

@section('content')
    <div class="center-form">
        <div class="page-header">
            <div>
                <div class="page-title">Create Return Request</div>
                <div class="page-sub">System has automatically detected expired and damaged items. Review the suggestions below, adjust quantities if needed, and submit.</div>
            </div>
            <div>
                <a href="{{ route('owner.return-requests.index') }}" class="btn-back">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
                    Back to Return Requests
                </a>
            </div>
        </div>


        @if($errors->any())
        <div class="error-box">
            <strong>Please fix the following errors:</strong>
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('owner.return-requests.store') }}" method="POST" id="returnForm">
            @csrf

            {{-- SECTION 1: Suggested Return Items --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        🔴 Suggested Return Items
                        <span style="font-size:0.75rem;font-weight:500;color:#7a8fa8;margin-left:4px;">Items flagged as expired or damaged in your inventory</span>
                    </div>
                    @if($suggestedItems->count() > 0)
                    <div style="display:flex;align-items:center;gap:12px;">
                        <button type="button" onclick="selectAll()" style="background:none;border:1px solid #d1dce8;border-radius:6px;padding:5px 12px;font-size:0.78rem;cursor:pointer;color:#3a4d6a;font-family:Inter,sans-serif;">Select All</button>
                        <button type="button" onclick="deselectAll()" style="background:none;border:1px solid #d1dce8;border-radius:6px;padding:5px 12px;font-size:0.78rem;cursor:pointer;color:#3a4d6a;font-family:Inter,sans-serif;">Deselect All</button>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    @forelse($suggestedItems as $index => $item)
                    @php
                        $defaultChecked = true;
                        $reasonDefault  = $item['is_expired'] ? 'expired' : 'damaged';
                        $colorClass     = $item['is_expired'] ? 'checked' : 'checked-damaged';
                    @endphp
                    <div class="suggestion-item {{ $colorClass }}" id="row_{{ $index }}" data-index="{{ $index }}">
                        <input
                            type="checkbox"
                            class="suggestion-checkbox"
                            id="cb_{{ $index }}"
                            {{ $defaultChecked ? 'checked' : '' }}
                            onchange="toggleItem({{ $index }})"
                        >
                        <div class="suggestion-info">
                            <div class="suggestion-name">{{ $item['item_name'] }}</div>
                            <div class="suggestion-meta">
                                <span>Supplier: <strong>{{ $item['supplier_name'] }}</strong></span>
                                <span class="dot">•</span>
                                <span>Invoice: <strong>{{ $item['invoice_number'] }}</strong></span>
                                <span class="dot">•</span>
                                <span>Available Qty: <strong>{{ $item['quantity'] }}</strong></span>
                                @if($item['is_expired'] && !empty($item['expiry_date']))
                                    <span class="dot">•</span>
                                    <span style="font-size:0.78rem;color:#c0392b;">Expired: <strong>{{ $item['expiry_date'] }}</strong></span>
                                @endif
                                @if($item['is_expired'])
                                    <span class="badge-expired">EXPIRED</span>
                                @endif
                                @if($item['is_damaged'])
                                    <span class="badge-damaged">DAMAGED</span>
                                @endif
                                {{-- Supplier return policy recommendation --}}
                                @if($item['is_expired'] || $item['is_damaged'])
                                    @if($item['accepts_returns'] ?? true)
                                        <span class="badge-return">✅ Return to Supplier</span>
                                    @else
                                        <span class="badge-dispose">⚠ Dispose</span>
                                    @endif
                                @endif
                            </div>

                            <div class="suggestion-controls" id="controls_{{ $index }}">
                                <div class="ctrl-group">
                                    <div class="ctrl-label">Return Qty</div>
                                    <input
                                        type="number"
                                        class="form-control qty-input"
                                        name="items[{{ $index }}][quantity]"
                                        id="qty_{{ $index }}"
                                        value="{{ $item['returnable_qty'] }}"
                                        min="1"
                                        max="{{ $item['returnable_qty'] }}"
                                        oninput="updateCounter()"
                                        required
                                    >
                                </div>
                                <div class="ctrl-group">
                                    <div class="ctrl-label">Reason</div>
                                    <select class="form-control" name="items[{{ $index }}][reason]" id="reason_{{ $index }}" onchange="toggleRemark({{ $index }})">
                                        @if($item['is_expired'])
                                            <option value="expired" selected>Expired</option>
                                        @endif
                                        @if($item['is_damaged'])
                                            <option value="damaged" {{ !$item['is_expired'] ? 'selected' : '' }}>Damaged</option>
                                        @endif
                                    </select>
                                </div>
                                {{-- Hidden fields for invoice/line traceability --}}
                                <input type="hidden" name="items[{{ $index }}][invoice_line_id]" value="{{ $item['invoice_line_id'] }}">
                                <input type="hidden" name="items[{{ $index }}][invoice_id]" value="{{ $item['invoice_id'] }}">
                            </div>

                            {{-- Damage remark – shown only when reason = damaged --}}
                            <div id="remark_wrap_{{ $index }}" style="margin-top:10px; display:{{ !$item['is_expired'] && $item['is_damaged'] ? 'block' : 'none' }};">
                                <div class="ctrl-label" style="margin-bottom:4px;">Damage Remark <span style="color:#e74c3c;">*</span></div>
                                <textarea
                                    class="form-control"
                                    name="items[{{ $index }}][damage_remark]"
                                    id="remark_{{ $index }}"
                                    rows="2"
                                    placeholder="Describe the damage (e.g. dent on packaging during storage)..."
                                    style="width:100%;max-width:480px;resize:vertical;"
                                    {{ !$item['is_expired'] && $item['is_damaged'] ? '' : 'disabled' }}
                                ></textarea>
                            </div>

                            {{-- Disabled placeholders when unchecked --}}
                            <div class="suggestion-controls" id="disabled_{{ $index }}" style="display:none;">
                                <span style="font-size:0.8rem;color:#9daec5;font-style:italic;">Item not selected for return</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="no-suggestions">
                        <div class="icon">✅</div>
                        <p>No expired or damaged items found</p>
                        <span>All your inventory items are in good condition with valid expiry dates.</span>
                    </div>
                    @endforelse

                    @if($suggestedItems->count() > 0)
                    <div class="counter-bar" id="counterBar">
                        <div class="counter-item">Selected Items: <strong id="selectedCount">{{ $suggestedItems->count() }}</strong></div>
                        <div class="counter-item">Total Return Quantity: <strong id="totalQty">{{ $suggestedItems->sum('returnable_qty') }}</strong></div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- SECTION 2: Return Request Preview --}}
            <div class="preview-card visible" id="previewCard">
                <div class="card-header" style="padding:18px 24px;">
                    <div class="card-title">📋 Return Request Preview</div>
                </div>
                <div class="preview-warning">
                    ⚠ Only selected items will be included in the Return Request. Items from different suppliers will create separate Return Requests automatically.
                </div>
                <div id="previewBody" style="padding-bottom:8px;"></div>
            </div>

            {{-- SECTION 3: Additional Notes --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">📝 Additional Notes <span style="font-weight:400;color:#9daec5;font-size:0.78rem;">(Optional)</span></div>
                </div>
                <div class="card-body">
                    <textarea name="notes" class="notes-area" placeholder="Add any additional notes or context about this return request..."></textarea>
                </div>
            </div>

            {{-- SECTION 4: Submit --}}
            <div class="action-row">
                <button type="button" class="btn-submit" id="submitBtn" onclick="confirmAndSubmit()">
                    ↩ Create Return Request
                </button>
                <a href="{{ route('owner.return-requests.index') }}" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
    </div>
@endsection

@push('scripts')
<script>
// Item data from server
const items = @json($suggestedItems);

function toggleItem(index) {
    const cb       = document.getElementById(`cb_${index}`);
    const row      = document.getElementById(`row_${index}`);
    const controls = document.getElementById(`controls_${index}`);
    const disabled = document.getElementById(`disabled_${index}`);
    const remarkWrap = document.getElementById(`remark_wrap_${index}`);
    const remarkEl  = document.getElementById(`remark_${index}`);
    const isChecked = cb.checked;

    // Toggle visual state
    const isExpired = items[index] && items[index].is_expired;
    const isDamaged = items[index] && items[index].is_damaged;
    if (isChecked) {
        row.className = `suggestion-item ${isDamaged && !isExpired ? 'checked-damaged' : 'checked'}`;
    } else {
        row.className = 'suggestion-item';
    }

    // Show/hide controls
    controls.style.display = isChecked ? 'flex' : 'none';
    disabled.style.display  = isChecked ? 'none' : 'flex';

    // Enable/disable inputs to prevent them from being submitted
    controls.querySelectorAll('input, select').forEach(el => el.disabled = !isChecked);

    // Handle remark wrap
    if (remarkWrap) {
        const reasonEl = document.getElementById(`reason_${index}`);
        const isDamagedReason = reasonEl && reasonEl.value === 'damaged';
        remarkWrap.style.display = isChecked && isDamagedReason ? 'block' : 'none';
        if (remarkEl) remarkEl.disabled = !(isChecked && isDamagedReason);
    }

    updateCounter();
    updatePreview();
}

function toggleRemark(index) {
    const reasonEl   = document.getElementById(`reason_${index}`);
    const remarkWrap = document.getElementById(`remark_wrap_${index}`);
    const remarkEl   = document.getElementById(`remark_${index}`);
    if (!remarkWrap || !remarkEl) return;
    const isDamaged = reasonEl && reasonEl.value === 'damaged';
    remarkWrap.style.display = isDamaged ? 'block' : 'none';
    remarkEl.disabled = !isDamaged;
    if (!isDamaged) remarkEl.value = '';
}

function selectAll() {
    items.forEach((_, i) => {
        const cb = document.getElementById(`cb_${i}`);
        if (!cb.checked) { cb.checked = true; toggleItem(i); }
    });
}

function deselectAll() {
    items.forEach((_, i) => {
        const cb = document.getElementById(`cb_${i}`);
        if (cb.checked) { cb.checked = false; toggleItem(i); }
    });
}

function updateCounter() {
    let selectedCount = 0, totalQty = 0;
    items.forEach((item, i) => {
        const cb  = document.getElementById(`cb_${i}`);
        const qty = document.getElementById(`qty_${i}`);
        if (cb && cb.checked) {
            selectedCount++;
            totalQty += parseInt(qty?.value || 0, 10);
        }
    });
    const scEl = document.getElementById('selectedCount');
    const tqEl = document.getElementById('totalQty');
    const sbBtn = document.getElementById('submitBtn');
    if (scEl) scEl.textContent = selectedCount;
    if (tqEl) tqEl.textContent = totalQty;
    if (sbBtn) sbBtn.disabled = selectedCount === 0;
    updatePreview();
}

function updatePreview() {
    const groups = {};
    items.forEach((item, i) => {
        const cb  = document.getElementById(`cb_${i}`);
        const qty = document.getElementById(`qty_${i}`);
        if (!cb || !cb.checked) return;
        const key = item.invoice_id;
        if (!groups[key]) {
            groups[key] = {
                supplier: item.supplier_name,
                invoice:  item.invoice_number,
                lines:    []
            };
        }
        groups[key].lines.push({
            name:   item.item_name,
            qty:    parseInt(qty?.value || 0, 10),
            uom:    item.uom,
            reason: item.is_expired ? 'Expired' : 'Damaged'
        });
    });

    const body    = document.getElementById('previewBody');
    const card    = document.getElementById('previewCard');
    const keys    = Object.keys(groups);

    if (keys.length === 0) {
        card.classList.remove('visible');
        return;
    }
    card.classList.add('visible');

    let html = '';
    keys.forEach((key, gi) => {
        const g = groups[key];
        html += `<div class="preview-group">
            <div class="preview-supplier">Supplier: ${g.supplier}</div>
            <div class="preview-invoice">Invoice: ${g.invoice}</div>
            ${g.lines.map(l => `
                <div class="preview-item">
                    <span>• ${l.name}</span>
                    <span style="color:#5a6a85;">Qty ${l.qty} ${l.uom} — <em>${l.reason}</em></span>
                </div>
            `).join('')}
        </div>`;
        if (gi < keys.length - 1) html += '<div class="preview-separator"></div>';
    });

    body.innerHTML = html;
}

function confirmAndSubmit() {
    const checked = items.filter((_, i) => document.getElementById(`cb_${i}`)?.checked);
    if (checked.length === 0) {
        alert('Please select at least one item to return.');
        return;
    }

    // Validate quantities and damage remarks
    for (let i = 0; i < items.length; i++) {
        const cb  = document.getElementById(`cb_${i}`);
        const qty = document.getElementById(`qty_${i}`);
        const reason = document.getElementById(`reason_${i}`);
        const remark = document.getElementById(`remark_${i}`);
        if (cb?.checked) {
            const q = parseInt(qty?.value || 0, 10);
            if (q <= 0) {
                alert(`Return quantity for "${items[i].item_name}" must be at least 1.`);
                qty.focus();
                return;
            }
            if (q > items[i].returnable_qty) {
                alert(`Return quantity for "${items[i].item_name}" cannot exceed ${items[i].returnable_qty}.`);
                qty.focus();
                return;
            }
            if (reason?.value === 'damaged' && remark && !remark.value.trim()) {
                alert(`A damage remark is required for "${items[i].item_name}" (damaged reason).`);
                remark.focus();
                return;
            }
        }
    }

    // Group by supplier for confirmation message
    const groups = {};
    items.forEach((item, i) => {
        const cb = document.getElementById(`cb_${i}`);
        if (cb?.checked) {
            const s = item.supplier_name;
            groups[s] = (groups[s] || 0) + 1;
        }
    });

    const supplierCount = Object.keys(groups).length;
    const rrCount       = supplierCount > 1 ? `${supplierCount} separate Return Requests (one per supplier)` : '1 Return Request';
    const msg = `You are about to create ${rrCount}.\n\nSuppliers:\n` +
        Object.entries(groups).map(([s, c]) => `  • ${s}: ${c} item(s)`).join('\n') +
        '\n\nProceed?';

    if (confirm(msg)) {
        document.getElementById('returnForm').submit();
    }
}

// Initialise on load
document.addEventListener('DOMContentLoaded', function () {
    updatePreview();
    updateCounter();
});
</script>
@endpush
