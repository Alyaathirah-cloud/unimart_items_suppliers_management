<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In — {{ config('app.name', '22UniMart') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(145deg, #eef1f8 0%, #e8ecf5 50%, #dde4f0 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        /* ── Brand header ── */
        .brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-name {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            color: #0f1f4b;
            text-transform: uppercase;
        }

        .brand-subtitle {
            font-size: 0.9rem;
            font-weight: 400;
            color: #6b7280;
            margin-top: 0.25rem;
            letter-spacing: 0.02em;
        }

        /* ── Card ── */
        .login-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 2.5rem 2.5rem 2rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 4px 32px rgba(15, 31, 75, 0.08);
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 1.75rem;
        }

        /* ── Form fields ── */
        .field-group {
            margin-bottom: 1.25rem;
        }

        .field-label {
            display: block;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 0.875rem;
            color: #9ca3af;
            font-size: 1rem;
            pointer-events: none;
            line-height: 1;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            color: #374151;
            background: #f9fafb;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            outline: none;
        }

        .form-input::placeholder {
            color: #9ca3af;
        }

        .form-input:focus {
            border-color: #0f1f4b;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(15, 31, 75, 0.08);
        }

        .form-input.is-invalid {
            border-color: #ef4444;
        }

        .form-input.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        /* Password toggle */
        .password-toggle {
            position: absolute;
            right: 0.875rem;
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            display: flex;
            align-items: center;
            padding: 0;
            transition: color 0.2s;
        }

        .password-toggle:hover {
            color: #4b5563;
        }

        /* Validation error */
        .invalid-message {
            font-size: 0.78rem;
            color: #ef4444;
            margin-top: 0.35rem;
        }

        /* Forgot password */
        .forgot-link {
            display: block;
            text-align: right;
            margin-top: 0.5rem;
            font-size: 0.82rem;
            color: #3b6bcc;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: #0f1f4b;
        }

        /* Sign In button */
        .btn-signin {
            display: block;
            width: 100%;
            padding: 0.875rem;
            margin-top: 1.5rem;
            background: #0f1f4b;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
        }

        .btn-signin:hover {
            background: #1a2f66;
            box-shadow: 0 4px 12px rgba(15, 31, 75, 0.25);
        }

        .btn-signin:active {
            transform: translateY(1px);
        }

        /* ── Footer ── */
        .page-footer {
            margin-top: 2rem;
            text-align: center;
            color: #9ca3af;
            font-size: 0.78rem;
        }

        .page-footer a {
            color: #9ca3af;
            text-decoration: none;
            transition: color 0.2s;
        }

        .page-footer a:hover {
            color: #374151;
        }

        .footer-links {
            margin-top: 0.35rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        /* SVG icons */
        .icon {
            width: 16px;
            height: 16px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
    </style>
</head>
<body>

    <!-- Brand -->
    <div class="brand">
        <div class="brand-name">22UniMart</div>
        <div class="brand-subtitle">Inventory Control</div>
    </div>

    <!-- Card -->
    <div class="login-card">
        <h1 class="card-title">Sign In</h1>

        @if (session('status'))
            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 12px 16px; color: #047857; font-size: 0.85rem; margin-bottom: 1.5rem; font-weight: 500;">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ $loginAction ?? route('login') }}">
            @csrf

            {{-- Email or Username --}}
            <div class="field-group">
                <label class="field-label" for="login">Email or Username</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg class="icon" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </span>
                    <input
                        id="login"
                        type="text"
                        name="login"
                        class="form-input @error('login') is-invalid @enderror"
                        value="{{ old('login') }}"
                        placeholder="Enter your email or username"
                        required
                        autocomplete="username"
                        autofocus
                    >
                </div>
                @error('login')
                    <div class="invalid-message">{{ $message }}</div>
                @enderror
            </div>

            {{-- Role Selection --}}
            <div class="field-group">
                <label class="field-label" for="role">Role</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg class="icon" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </span>
                    <select id="role" name="role" class="form-input @error('role') is-invalid @enderror" required style="-webkit-appearance: none; -moz-appearance: none; appearance: none; cursor: pointer;">
                        <option value="" disabled selected>Select Role</option>
                        <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>Owner</option>
                        <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="supplier" {{ old('role') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                    </select>
                </div>
                @error('role')
                    <div class="invalid-message">{{ $message }}</div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="field-group">
                <label class="field-label" for="password">Password</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg class="icon" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </span>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="form-input @error('password') is-invalid @enderror"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                    >
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

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                @endif
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-signin">Sign In</button>

        </form>
    </div>

    <!-- Footer -->
    <footer class="page-footer">
        <div>© {{ date('Y') }} 22UniMart. All rights reserved.</div>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
        </div>
    </footer>

    <script>
        // Password visibility toggle
        const toggle = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        const eyeOpenPath = `
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
        `;
        const eyeClosedPath = `
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
            <line x1="1" y1="1" x2="23" y2="23"/>
        `;

        toggle.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            eyeIcon.innerHTML = isPassword ? eyeOpenPath : eyeClosedPath;
        });
    </script>

</body>
</html>
