<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Change Password – Supplier Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(180deg, #07152e, #0f2044 42%, #10244a); color: #1a2744; }
        .auth-shell { width: min(560px, calc(100% - 24px)); background: #fff; border-radius: 18px; box-shadow: 0 18px 60px rgba(3,10,24,0.30); overflow: hidden; }
        .auth-header { padding: 26px 28px 12px; border-bottom: 1px solid #edf2f7; }
        .brand { font-size: 1.5rem; font-weight: 800; color: #0f2044; display:flex; align-items:center; gap:10px; }
        .brand-badge { width: 28px; height: 28px; border-radius: 7px; background:#0f2044; color:#fff; display:flex; align-items:center; justify-content:center; font-size: .8rem; font-weight:800; }
        .auth-title { margin-top: 14px; font-size: 1.5rem; font-weight: 800; color:#0f2044; }
        .auth-copy { margin-top: 6px; font-size: .92rem; color:#5a6a85; line-height:1.5; }
        .auth-body { padding: 24px 28px 28px; }
        .flash { border-radius: 10px; padding: 12px 14px; margin-bottom: 16px; font-size: .82rem; line-height:1.4; }
        .flash-success { background:#e9f8ef; color:#1d8348; border:1px solid #a9dfbf; }
        .flash-error { background:#fdecea; color:#c0392b; border:1px solid #f5b7b1; }
        .form-grid { display:flex; flex-direction:column; gap: 14px; }
        .form-group { display:flex; flex-direction:column; gap:8px; }
        .form-label { font-size:.74rem; font-weight:700; text-transform:uppercase; color:#6f7f95; letter-spacing:.5px; }
        .form-control { border:1px solid #d0d8e5; border-radius:10px; padding:12px 13px; font-size:.95rem; font-family:'Inter', sans-serif; color:#1a2744; background:#fff; }
        .form-control:focus { outline:none; border-color:#0f2044; box-shadow:0 0 0 4px rgba(15,32,68,0.12); }
        .helper { font-size:.74rem; color:#7a8fa8; }
        .btn-submit { margin-top: 4px; border:none; border-radius:10px; padding:13px 14px; background:#0f2044; color:#fff; cursor:pointer; font-size:.93rem; font-weight:700; }
        .error-msg { font-size:.72rem; color:#c0392b; font-weight:600; }
    </style>
</head>
<body>
    <div class="auth-shell">
        <div class="auth-header">
            <div class="brand"><span class="brand-badge">22</span> 22UNIMART</div>
            <div class="auth-title">Password Reset Required</div>
            <div class="auth-copy">For your security, please set a new password before continuing. Use a strong password and keep it private.</div>
        </div>

        <div class="auth-body">
            @if(session('success'))
                <div class="flash flash-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="flash flash-error">{{ session('error') }}</div>
            @endif

            <form action="{{ route('supplier.change-password.update') }}" method="POST">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="password">New Password</label>
                        <input type="password" id="password" name="password" class="form-control" required minlength="8" autocomplete="new-password">
                        @error('password')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required minlength="8" autocomplete="new-password">
                    </div>
                    <div class="helper">Passwords must be at least 8 characters and must not match your current password.</div>
                    <button type="submit" class="btn-submit">Update Password & Continue</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
