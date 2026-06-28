<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account – 22UniMart</title>
    <meta name="description" content="Create your 22UniMart account to manage inventory and suppliers.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #eef2f7;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Top branding ── */
        .top-brand {
            text-align: center;
            padding: 44px 24px 24px;
        }
        .top-brand .brand-name {
            font-size: 1.9rem;
            font-weight: 800;
            color: #0f2044;
            letter-spacing: -0.5px;
        }
        .top-brand .brand-sub {
            margin-top: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #7a8fa8;
        }

        /* ── Card ── */
        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 40px rgba(15,32,68,0.1);
            width: 100%;
            max-width: 420px;
            margin: 0 auto 40px;
            padding: 36px 40px 32px;
        }

        .card-title {
            font-size: 1.45rem;
            font-weight: 700;
            color: #0f2044;
            text-align: center;
            margin-bottom: 24px;
        }

        /* ── Form fields ── */
        .field {
            margin-bottom: 20px;
        }
        .field label {
            display: block;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #3a4d6a;
            margin-bottom: 8px;
        }
        .input-wrap {
            display: flex;
            align-items: center;
            border-bottom: 1.5px solid #c8d5e8;
            padding-bottom: 8px;
            transition: border-color 0.2s;
        }
        .input-wrap:focus-within {
            border-color: #0f2044;
        }
        .input-icon {
            color: #9daec5;
            margin-right: 10px;
            flex-shrink: 0;
            font-size: 1rem;
            width: 18px;
            text-align: center;
        }
        .input-wrap input {
            flex: 1;
            border: none;
            outline: none;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            color: #1a2744;
            background: transparent;
        }
        .input-wrap input::placeholder { color: #b0bdd0; }
        .toggle-pw {
            background: none;
            border: none;
            cursor: pointer;
            color: #9daec5;
            padding: 0;
            display: flex;
            align-items: center;
        }
        .toggle-pw:hover { color: #3a4d6a; }

        /* Validation errors */
        .error-msg {
            color: #d9534f;
            font-size: 0.8rem;
            margin-top: 6px;
        }

        /* ── Submit button ── */
        .btn-submit {
            width: 100%;
            padding: 15px;
            background-color: #0f2044;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background-color 0.2s, transform 0.15s;
        }
        .btn-submit:hover {
            background-color: #182e5e;
            transform: translateY(-1px);
        }

        /* ── Sign in link ── */
        .signin-hint {
            text-align: center;
            margin-top: 20px;
            font-size: 0.88rem;
            color: #7a8fa8;
        }
        .signin-hint a {
            color: #3a7bd5;
            text-decoration: none;
            font-weight: 600;
        }
        .signin-hint a:hover { text-decoration: underline; }

        /* ── Footer ── */
        footer {
            margin-top: auto;
            padding: 20px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            border-top: 1px solid #dde3ec;
        }
        .footer-brand {
            font-size: 0.9rem;
            font-weight: 700;
            color: #0f2044;
        }
        .footer-links {
            display: flex;
            gap: 24px;
        }
        .footer-links a {
            font-size: 0.82rem;
            color: #5a6a85;
            text-decoration: none;
        }
        .footer-links a:hover { text-decoration: underline; }
        .footer-copy {
            font-size: 0.82rem;
            color: #7a8fa8;
        }

        /* ── Alert errors ── */
        .alert-danger {
            background: #fdeaea;
            border: 1px solid #f5c6c6;
            border-radius: 8px;
            padding: 12px 16px;
            color: #c0392b;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }
        .alert-danger ul { padding-left: 16px; margin-top: 6px; }
    </style>
</head>
<body>

    <!-- Top brand -->
    <div class="top-brand">
        <div class="brand-name">22UniMart</div>
        <div class="brand-sub">Inventory Control</div>
    </div>

    <!-- Register card -->
    <div class="card">
        <h1 class="card-title">Create Account</h1>

        @if ($errors->any())
            <div class="alert-danger">
                <strong>Please fix the following errors:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" id="registerForm">
            @csrf

            <!-- Full Name -->
            <div class="field">
                <label for="name">Full Name</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </span>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           placeholder="John Doe" required autocomplete="name" autofocus>
                </div>
                @error('name')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- Role Selection -->
            <div class="field">
                <label for="role">Select Role</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </span>
                    <select id="role" name="role" required style="flex: 1; border: none; outline: none; font-family: 'Inter', sans-serif; font-size: 0.95rem; color: #1a2744; background: transparent; -webkit-appearance: none; -moz-appearance: none; appearance: none; cursor: pointer;">
                        <option value="" disabled selected>Choose your role...</option>
                        <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>Owner</option>
                        <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="supplier" {{ old('role') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                    </select>
                </div>
                @error('role')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email -->
            <div class="field">
                <label for="email">Email or Username</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </span>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           placeholder="john@example.com" required autocomplete="email">
                </div>
                @error('email')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>
            <!-- Password -->
            <div class="field">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                    <input type="password" id="password" name="password"
                           placeholder="••••••••" required autocomplete="new-password">
                    <button type="button" class="toggle-pw" onclick="togglePw('password', 'eyeIcon1')" aria-label="Toggle password visibility">
                        <svg id="eyeIcon1" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
                @error('password')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="field">
                <label for="password_confirmation">Confirm Password</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           placeholder="••••••••" required autocomplete="new-password">
                    <button type="button" class="toggle-pw" onclick="togglePw('password_confirmation', 'eyeIcon2')" aria-label="Toggle confirm password visibility">
                        <svg id="eyeIcon2" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit" id="btn-signup">
                Sign Up
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
        </form>

        <p class="signin-hint">
            Already have an account? <a href="{{ route('login') }}">Sign In</a>
        </p>
    </div>

    <!-- Footer -->
    <footer>
        <span class="footer-brand">22UniMart</span>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Help Center</a>
        </div>
        <span class="footer-copy">&copy; {{ date('Y') }} 22UniMart. All rights reserved.</span>
    </footer>

    <script>
        function togglePw(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            // Switch icon between slash-eye and normal eye
            icon.innerHTML = isHidden
                ? '<eye-on><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></eye-on>'
                : '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';

            if (isHidden) {
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            } else {
                icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';
            }
        }
    </script>

</body>
</html>
