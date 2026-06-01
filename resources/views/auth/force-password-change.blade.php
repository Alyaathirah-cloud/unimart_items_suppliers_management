<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Change Password – 22UniMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f6fb; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .auth-card { background: #fff; width: 100%; max-width: 400px; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(15,32,68,0.05); }
        .auth-brand { text-align: center; margin-bottom: 30px; }
        .auth-brand .square { width: 40px; height: 40px; background: #0f2044; color: #fff; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; font-weight: 800; font-size: 1.2rem; margin-bottom: 10px; }
        .auth-title { font-size: 1.25rem; font-weight: 700; color: #0f2044; text-align: center; margin-bottom: 8px; }
        .auth-desc { font-size: 0.85rem; color: #5a6a85; text-align: center; margin-bottom: 24px; }
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 0.8rem; font-weight: 600; color: #3a4d6a; margin-bottom: 6px; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.9rem; font-family: inherit; transition: border-color 0.15s; box-sizing: border-box; }
        .form-control:focus { outline: none; border-color: #4a90d9; box-shadow: 0 0 0 3px rgba(74,144,217,0.1); }
        .btn-submit { width: 100%; background: #0f2044; color: #fff; border: none; padding: 12px; border-radius: 6px; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.15s; margin-top: 8px; }
        .btn-submit:hover { background: #182e5e; }
        .error-msg { font-size: 0.8rem; color: #c0392b; margin-top: 4px; }
        .alert { padding: 12px; border-radius: 6px; font-size: 0.85rem; margin-bottom: 20px; text-align: center; }
        .alert-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-brand">
            <div class="square">22</div>
            <div style="font-weight: 800; color: #0f2044; letter-spacing: 0.5px;">22UNIMART</div>
        </div>
        <div class="auth-title">Update Your Password</div>
        <div class="auth-desc">For your security, please set a new password before continuing.</div>

        @if(session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif

        <form method="POST" action="{{ route('password.force-change.update') }}">
            @csrf
            <div class="form-group">
                <label class="form-label" for="password">New Password</label>
                <input type="password" id="password" name="password" class="form-control" required autofocus>
                @error('password')<div class="error-msg">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
            </div>
            <button type="submit" class="btn-submit">Set New Password</button>
        </form>
    </div>
</body>
</html>
