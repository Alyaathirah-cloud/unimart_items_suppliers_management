<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Staff Login — {{ config('app.name', '22UniMart') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; min-height: 100vh; background: linear-gradient(145deg, #eef1f8 0%, #e8ecf5 50%, #dde4f0 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2rem 1rem; }
        .brand { text-align: center; margin-bottom: 2rem; }
        .brand-name { font-size: 2rem; font-weight: 800; letter-spacing: 0.12em; color: #0f1f4b; text-transform: uppercase; }
        .brand-subtitle { font-size: 0.9rem; font-weight: 400; color: #6b7280; margin-top: 0.25rem; letter-spacing: 0.02em; }
        .login-card { background: #ffffff; border-radius: 16px; padding: 2.5rem 2.5rem 2rem; width: 100%; max-width: 420px; box-shadow: 0 4px 32px rgba(15, 31, 75, 0.08); }
        .card-title { font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: 1.75rem; text-align: center; }
        .field-group { margin-bottom: 1.25rem; }
        .field-label { display: block; font-size: 0.7rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: #6b7280; margin-bottom: 0.5rem; }
        .input-wrapper { position: relative; display: flex; align-items: center; }
        .input-icon { position: absolute; left: 0.875rem; color: #9ca3af; font-size: 1rem; pointer-events: none; line-height: 1; }
        .form-input { width: 100%; padding: 0.75rem 1rem 0.75rem 2.75rem; border: 1.5px solid #e5e7eb; border-radius: 10px; font-family: 'Inter', sans-serif; font-size: 0.9rem; color: #374151; background: #f9fafb; transition: border-color 0.2s, box-shadow 0.2s, background 0.2s; outline: none; }
        .form-input::placeholder { color: #9ca3af; }
        .form-input:focus { border-color: #0f1f4b; background: #ffffff; box-shadow: 0 0 0 3px rgba(15, 31, 75, 0.08); }
        .form-input.is-invalid { border-color: #ef4444; }
        .form-input.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1); }
        .password-toggle { position: absolute; right: 0.875rem; background: none; border: none; cursor: pointer; color: #9ca3af; display: flex; align-items: center; padding: 0; transition: color 0.2s; }
        .password-toggle:hover { color: #4b5563; }
        .invalid-message { font-size: 0.78rem; color: #ef4444; margin-top: 0.35rem; }
        .btn-signin { display: block; width: 100%; padding: 0.875rem; margin-top: 1.5rem; background: #0f1f4b; color: #ffffff; border: none; border-radius: 10px; font-family: 'Inter', sans-serif; font-size: 0.95rem; font-weight: 600; letter-spacing: 0.02em; cursor: pointer; transition: background 0.2s, transform 0.1s, box-shadow 0.2s; }
        .btn-signin:hover { background: #1a2f66; box-shadow: 0 4px 12px rgba(15, 31, 75, 0.25); }
        .btn-signin:active { transform: translateY(1px); }
        .page-footer { margin-top: 2rem; text-align: center; color: #9ca3af; font-size: 0.78rem; }
        .icon { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
    </style>
</head>
<body>

    <div class="brand">
        <div class="brand-name">22UniMart</div>
        <div class="brand-subtitle">Inventory Control</div>
    </div>

    <div class="login-card">
        <h1 class="card-title">Staff Login</h1>

        <form method="POST" action="{{ route('staff.login') }}">
            @csrf

            <div class="field-group">
                <label class="field-label" for="email">Email</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg class="icon" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </span>
                    <input id="email" type="email" name="email" class="form-input @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Enter your email" required autofocus>
                </div>
                @error('email')
                    <div class="invalid-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label" for="password">Password</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg class="icon" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </span>
                    <input id="password" type="password" name="password" class="form-input @error('password') is-invalid @enderror" placeholder="••••••••" required>
                    <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password visibility">
                        <svg class="icon" id="eyeIcon" viewBox="0 0 24 24">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-signin">Sign In</button>
            
            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="{{ route('login') }}" style="color: #3b6bcc; text-decoration: none; font-size: 0.85rem; font-weight: 500;">Are you the owner? Login here</a>
            </div>

        </form>
    </div>

    <footer class="page-footer">
        <div>© {{ date('Y') }} 22UniMart. All rights reserved.</div>
    </footer>

    <script>
        const toggle = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeOpenPath = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
        const eyeClosedPath = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';

        toggle.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            eyeIcon.innerHTML = isPassword ? eyeOpenPath : eyeClosedPath;
        });
    </script>
</body>
</html>
