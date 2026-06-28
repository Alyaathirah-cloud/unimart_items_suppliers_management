<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notifications – Supplier | 22UniMart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #eef2f7; display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar { width: 210px; flex-shrink: 0; background: #0D1B2A; color: #fff; display: flex; flex-direction: column; padding: 0 0 24px; position: fixed; top: 0; left: 0; height: 100vh; z-index: 50; }
        .sidebar-brand { padding: 20px 20px 12px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid rgba(255,255,255,0.07); margin-bottom: 10px; }
        .brand-sq { width: 32px; height: 32px; background: #fff; color: #0D1B2A; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: .9rem; font-weight: 800; flex-shrink: 0; }
        .brand-name { font-size: .9rem; font-weight: 800; }
        .brand-sub  { font-size: .68rem; color: #6b84a3; }
        .sidebar-nav { flex: 1; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; font-size: .88rem; font-weight: 500; color: #7a95b5; text-decoration: none; border-left: 3px solid transparent; transition: all .15s; }
        .nav-item:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .nav-item.active { color: #fff; background: rgba(255,255,255,0.1); border-left-color: #10b981; font-weight: 700; }
        .nav-icon { width: 18px; text-align: center; flex-shrink: 0; }
        .sidebar-bottom { padding: 16px 20px 0; border-top: 1px solid rgba(255,255,255,0.07); margin-top: auto; display: flex; flex-direction: column; gap: 8px; }
        .btn-report { background: #1e3a6e; color: #fff; border: none; border-radius: 8px; padding: 12px 16px; font-size: 0.85rem; font-weight: 600; cursor: pointer; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s; }
        .btn-report:hover { background: #2a4f8f; }
        .btn-logout { background: rgba(220,53,69,.15); color: #f87171; border: 1px solid rgba(220,53,69,.3); border-radius: 7px; padding: 10px 14px; font-size: .82rem; font-weight: 600; cursor: pointer; width: 100%; font-family: 'Inter', sans-serif; transition: all .15s; }
        .btn-logout:hover { background: rgba(220,53,69,.25); }

        /* ── Layout ── */
        .main { margin-left: 210px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0 32px; height: 56px; display: flex; align-items: center; gap: 16px; position: sticky; top: 0; z-index: 10; }
        .topbar-label { font-size: .9rem; font-weight: 700; color: #0D1B2A; }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 12px; }
        .avatar { width: 36px; height: 36px; border-radius: 50%; background: #0D1B2A; display: flex; align-items: center; justify-content: center; color: #fff; font-size: .85rem; font-weight: 700; flex-shrink: 0; }
        .topbar-name { font-size: .85rem; font-weight: 600; color: #1a2744; }
        .topbar-logout { background: none; border: none; font-size: .72rem; color: #9daec5; cursor: pointer; font-family: inherit; padding: 0; }

        /* ── Content ── */
        .content { padding: 32px; max-width: 1000px; margin: 0 auto; width: 100%; }
        .page-header { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 24px; }
        .page-title { font-size: 1.6rem; font-weight: 800; color: #0D1B2A; }
        .page-sub   { font-size: .85rem; color: #7a8fa8; margin-top: 5px; }

        .btn-mark-all { background: #fff; border: 1px solid #d1dce8; border-radius: 8px; padding: 8px 16px; font-size: 0.85rem; font-weight: 600; color: #3a4d6a; cursor: pointer; font-family: 'Inter', sans-serif; transition: background 0.15s; }
        .btn-mark-all:hover { background: #f4f6fb; }

        .notif-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); overflow: hidden; }
        .notif-item { display: flex; gap: 16px; padding: 20px; border-bottom: 1px solid #f0f4f8; transition: background 0.15s; }
        .notif-item:last-child { border-bottom: none; }
        .notif-item.unread { background: #f8fafc; border-left: 3px solid #10b981; padding-left: 17px; }
        
        .notif-icon { width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
        .icon-approve { background: #e8f8f0; color: #1d8348; }
        .icon-reject  { background: #fdedec; color: #c0392b; }
        .icon-deliver { background: #e8f4fd; color: #2980b9; }
        .icon-info    { background: #fef3e2; color: #d4870a; }

        .notif-content { flex: 1; }
        .notif-title { font-size: 0.95rem; font-weight: 700; color: #0D1B2A; margin-bottom: 4px; }
        .notif-message { font-size: 0.85rem; color: #5a6a85; line-height: 1.4; }
        .notif-meta { display: flex; align-items: center; gap: 12px; margin-top: 8px; font-size: 0.75rem; color: #9daec5; font-weight: 500; }
        .notif-time { color: #7a8fa8; }

        .btn-read { background: none; border: none; font-size: 0.75rem; font-weight: 600; color: #10b981; cursor: pointer; padding: 0; margin-left: auto; font-family: 'Inter', sans-serif; }
        .btn-read:hover { text-decoration: underline; }

        .empty-state { padding: 60px 20px; text-align: center; color: #9daec5; }
        .empty-icon { font-size: 2.5rem; margin-bottom: 12px; }
        .empty-text { font-size: 0.95rem; font-weight: 600; color: #7a8fa8; }

        .pagination-wrap { padding: 16px 20px; background: #fafbfd; border-top: 1px solid #f0f4f8; display: flex; justify-content: space-between; align-items: center; }
        .footer-info { font-size: 0.8rem; color: #7a8fa8; }
        .page-btn { width: 32px; height: 32px; border-radius: 7px; border: 1px solid #e2e8f0; background: #fff; color: #3a4d6a; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.85rem; text-decoration: none; }
        .page-btn:hover { background: #0D1B2A; color: #fff; border-color: #0D1B2A; }
        .page-btn.disabled { opacity: 0.4; pointer-events: none; }
    </style>
</head>
<body>

@include('supplier.partials.sidebar', ['active' => 'notifications', 'hideBrandSub' => true])

<div class="main">
    <div class="topbar">
        <div class="topbar-label">Notifications</div>
        <div class="topbar-right" style="margin-left: auto; display: flex; align-items: center; gap: 20px;">
            @include('supplier.components.topbar-profile')
        </div>
    </div>

    <div class="content">
        <div class="page-header">
            <div>
                <div class="page-title">Notifications</div>
                <div class="page-sub">Updates on purchase orders, return requests, and supplier deliveries.</div>
            </div>
            @if($notifications->whereNull('read_at')->count() > 0)
            <form action="{{ route('supplier.notifications.markAll') }}" method="POST">
                @csrf
                <button type="submit" class="btn-mark-all">✓ Mark All as Read</button>
            </form>
            @endif
        </div>

        <div class="notif-card">
            @forelse($notifications as $notif)
                @php
                    $isUnread = is_null($notif->read_at);
                    
                    // Determine icon
                    $iconClass = 'icon-info';
                    $icon = 'ℹ️';
                    if(str_contains($notif->type, 'approve') || str_contains($notif->type, 'credit_note')) {
                        $iconClass = 'icon-approve'; $icon = '✔';
                    } elseif(str_contains($notif->type, 'reject')) {
                        $iconClass = 'icon-reject'; $icon = '✕';
                    } elseif(str_contains($notif->type, 'deliver')) {
                        $iconClass = 'icon-deliver'; $icon = '📦';
                    }

                    // Formatted title
                    $title = 'System Notification';
                    if(str_contains($notif->type, 'purchase_order')) $title = 'Purchase Order Update';
                    if(str_contains($notif->type, 'return_request')) $title = 'Return Request Update';
                @endphp
                
                <div class="notif-item {{ $isUnread ? 'unread' : '' }}">
                    <div class="notif-icon {{ $iconClass }}">{{ $icon }}</div>
                    <div class="notif-content">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                            <div class="notif-title">{{ $title }}</div>
                            @if($isUnread)
                                <form action="{{ route('supplier.notifications.markAsRead', $notif) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-read">Mark as read</button>
                                </form>
                            @endif
                        </div>
                        <div class="notif-message">{{ $notif->message }}</div>
                        <div class="notif-meta">
                            <span class="notif-time">{{ $notif->created_at->diffForHumans() }}</span>
                            @if(isset($notif->data['po_number']))
                                <span>PO: #{{ $notif->data['po_number'] }}</span>
                            @endif
                            @if(isset($notif->data['return_number']))
                                <span>RR: #{{ $notif->data['return_number'] }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">📭</div>
                    <div class="empty-text">You're all caught up!</div>
                    <div style="font-size:0.85rem; margin-top:4px;">No new notifications to show.</div>
                </div>
            @endforelse

            @if($notifications->hasPages())
            <div class="pagination-wrap">
                <div class="footer-info">Showing {{ $notifications->firstItem() ?? 0 }}–{{ $notifications->lastItem() ?? 0 }} of {{ $notifications->total() }}</div>
                <div style="display:flex;align-items:center;gap:8px;">
                    @if($notifications->onFirstPage())
                        <span class="page-btn disabled">‹</span>
                    @else
                        <a class="page-btn" href="{{ $notifications->previousPageUrl() }}">‹</a>
                    @endif
                    <span style="font-size:0.8rem;color:#7a8fa8;">Page {{ $notifications->currentPage() }} / {{ $notifications->lastPage() }}</span>
                    @if($notifications->hasMorePages())
                        <a class="page-btn" href="{{ $notifications->nextPageUrl() }}">›</a>
                    @else
                        <span class="page-btn disabled">›</span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
</body>
</html>
