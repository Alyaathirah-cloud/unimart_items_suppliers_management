<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>22UniMart – Items & Supplier Management</title>
    <meta name="description" content="The all-in-one inventory and supplier management system built for small businesses.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #eef2f7;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Main content ── */
        .page-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 60px 24px 40px;
        }

        .hero-title {
            font-size: clamp(2.4rem, 7vw, 4.8rem);
            font-weight: 900;
            color: #1a2744;
            line-height: 1.1;
            letter-spacing: -1px;
            max-width: 740px;
        }

        .hero-subtitle {
            margin-top: 28px;
            font-size: 1.05rem;
            color: #5a6a85;
            max-width: 480px;
            line-height: 1.7;
        }

        /* ── Buttons ── */
        .btn-group {
            display: flex;
            gap: 16px;
            margin-top: 44px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 16px 44px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
            border: none;
        }

        .btn-primary {
            background-color: #0f2044;
            color: #fff;
        }
        .btn-primary:hover {
            background-color: #182e5e;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(15,32,68,0.3);
        }

        .btn-secondary {
            background-color: #dce4ef;
            color: #3a4d6a;
        }
        .btn-secondary:hover {
            background-color: #c8d5e8;
            transform: translateY(-1px);
        }

        /* ── Already logged in hint ── */
        .already-logged {
            margin-top: 28px;
            font-size: 0.9rem;
            color: #7a8fa8;
        }
        .already-logged a {
            color: #3a7bd5;
            text-decoration: none;
            font-weight: 500;
        }
        .already-logged a:hover { text-decoration: underline; }

        /* ── Footer ── */
        footer {
            text-align: center;
            padding: 28px 24px;
            border-top: 1px solid #dde3ec;
        }
        .footer-brand {
            font-size: 1rem;
            font-weight: 700;
            color: #1a2744;
        }
        .footer-copy {
            margin-top: 6px;
            font-size: 0.82rem;
            color: #7a8fa8;
        }
        .footer-copy a {
            color: #3a7bd5;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="page-wrapper">
        <h1 class="hero-title">22UniMart Items and Supplier Management System</h1>
        <p class="hero-subtitle">
            The all-in-one inventory and supplier management system built for small
            businesses. Elevate your operations from chaotic to curated.
        </p>

        <div class="btn-group">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn btn-primary" id="btn-signup">Sign Up</a>
            @endif
            @if (Route::has('login'))
                <a href="{{ route('login') }}" class="btn btn-secondary" id="btn-login">Login</a>
            @endif
        </div>

        @auth
            <p class="already-logged">
                Already logged in? <a href="{{ url('/home') }}">Go to Dashboard</a>
            </p>
        @else
            <p class="already-logged">
                Already logged in? <a href="{{ route('login') }}">Go to Dashboard</a>
            </p>
        @endauth
    </div>

    <footer>
        <p class="footer-brand">22UniMart</p>
        <p class="footer-copy">
            &copy; {{ date('Y') }} 22UniMart. <a href="#">Precision in Every Unit.</a>
        </p>
    </footer>

</body>
</html>
