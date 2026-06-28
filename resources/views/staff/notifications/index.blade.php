@extends('layouts.staff')

@section('title', 'Notifications – 22UniMart')

@push('styles')
    <style>
        .page-header { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 28px; }
        .page-title { font-size: 1.6rem; font-weight: 800; color: #0f2044; }
        .page-sub   { font-size: 0.85rem; color: #7a8fa8; margin-top: 4px; }
        .btn-mark-all { background: #fff; border: 1px solid #d1dce8; border-radius: 8px; padding: 8px 16px; font-size: 0.85rem; font-weight: 600; color: #3a4d6a; cursor: pointer; font-family: 'Inter', sans-serif; transition: background 0.15s; }
        .btn-mark-all:hover { background: #f4f6fb; }

        .notif-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); overflow: hidden; }
        .notif-item { display: flex; gap: 16px; padding: 20px; border-bottom: 1px solid #f0f4f8; transition: background 0.15s; }
        .notif-item:last-child { border-bottom: none; }
        .notif-item.unread { background: #f8fafc; border-left: 3px solid #4a90d9; padding-left: 17px; }
        
        .notif-icon { width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
        .icon-approve { background: #e8f8f0; color: #1d8348; }
        .icon-reject  { background: #fdedec; color: #c0392b; }
        .icon-deliver { background: #e8f4fd; color: #2980b9; }
        .icon-info    { background: #fef3e2; color: #d4870a; }

        .notif-content { flex: 1; }
        .notif-title { font-size: 0.95rem; font-weight: 700; color: #0f2044; margin-bottom: 4px; }
        .notif-message { font-size: 0.85rem; color: #5a6a85; line-height: 1.4; }
        .notif-meta { display: flex; align-items: center; gap: 12px; margin-top: 8px; font-size: 0.75rem; color: #9daec5; font-weight: 500; }
        .notif-time { color: #7a8fa8; }

        .btn-read { background: none; border: none; font-size: 0.75rem; font-weight: 600; color: #4a90d9; cursor: pointer; padding: 0; margin-left: auto; font-family: 'Inter', sans-serif; }
        .btn-read:hover { text-decoration: underline; }

        .empty-state { padding: 60px 20px; text-align: center; color: #9daec5; }
        .empty-icon { font-size: 2.5rem; margin-bottom: 12px; }
        .empty-text { font-size: 0.95rem; font-weight: 600; color: #7a8fa8; }

        .pagination-wrap { display: flex; align-items: center; gap: 8px; }
        .footer-info { font-size: 0.8rem; color: #7a8fa8; }
        .page-btn { width: 32px; height: 32px; border-radius: 7px; border: 1px solid #e2e8f0; background: #fff; color: #3a4d6a; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.85rem; text-decoration: none; font-family: 'Inter', sans-serif; transition: all 0.12s; }
        .page-btn:hover { background: #0f2044; color: #fff; border-color: #0f2044; }
        .page-btn.disabled { opacity: 0.4; pointer-events: none; }
        .page-btn.active { background: #0f2044; color: #fff; border-color: #0f2044; }
        
        .topbar-search { display: flex; align-items: center; gap: 8px; background: #f4f6fb; border-radius: 8px; padding: 8px 14px; flex: 1; max-width: 360px; margin-right: auto; }
        .topbar-search input { border: none; background: transparent; outline: none; font-family: 'Inter', sans-serif; font-size: 0.88rem; color: #3a4d6a; width: 100%; }
        .topbar-search input::placeholder { color: #9daec5; }
        .notif-pagination-container { padding: 16px 20px; background: #fafbfd; border-top: 1px solid #f0f4f8; display: flex; justify-content: space-between; align-items: center; }
    </style>
@endpush

@section('topbar')
    <div class="topbar-search">
        <svg width="15" height="15" fill="none" stroke="#9daec5" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" placeholder="Search inventory, POs...">
    </div>
    <div class="topbar-right">
        <a href="{{ route('staff.notif.index') }}" class="icon-btn" style="text-decoration:none;">🔔</a>
        @include('staff.components.topbar-profile')
    </div>
@endsection

@section('content')
        <div class="page-header">
            <div>
                <div class="page-title">Notifications</div>
                <div class="page-sub">Updates on purchase orders, return requests, and supplier deliveries.</div>
            </div>
            @if($notifications->whereNull('read_at')->count() > 0)
            <form action="{{ route('staff.notifications.markAll') }}" method="POST">
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
                                <form action="{{ route('staff.notifications.markAsRead', $notif) }}" method="POST">
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
            <div class="notif-pagination-container">
                <div class="footer-info">Showing {{ $notifications->firstItem() ?? 0 }}–{{ $notifications->lastItem() ?? 0 }} of {{ $notifications->total() }}</div>
                <div class="pagination-wrap">
                    @if($notifications->onFirstPage())
                        <span class="page-btn disabled">‹</span>
                    @else
                        <a href="{{ $notifications->previousPageUrl() }}" class="page-btn">‹</a>
                    @endif

                    @foreach(range(1, max(1, $notifications->lastPage())) as $i)
                        @if($i >= $notifications->currentPage() - 2 && $i <= $notifications->currentPage() + 2)
                            @if($i == $notifications->currentPage())
                                <span class="page-btn active">{{ $i }}</span>
                            @else
                                <a href="{{ $notifications->url($i) }}" class="page-btn">{{ $i }}</a>
                            @endif
                        @endif
                    @endforeach

                    @if($notifications->hasMorePages())
                        <a href="{{ $notifications->nextPageUrl() }}" class="page-btn">›</a>
                    @else
                        <span class="page-btn disabled">›</span>
                    @endif
                </div>
            </div>
            @endif
        </div>
@endsection
