<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Movie Database')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a237e 0%, #0d47a1 40%, #e65100 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            background: #212121;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            border-radius: 10px;
        }

        .header h1 {
            ccolor: #ffffff;
            font-size: 24px;
        }

        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .lang-switch {
            display: flex;
            gap: 5px;
        }

        .lang-btn {
            padding: 5px 12px;
            border: 2px solid #ffffff;
            background: transparent;
            color: #ffffff;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .lang-btn.active,
        .lang-btn:hover {
            background: #ffffff;
            color: #212121;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
        }

        .btn-danger {
            background: #f44336;
            color: white;
        }

        .btn-danger:hover {
            background: #da190b;
        }

        /* Login Page */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }

        .login-box {
            background: #1a1a1a;
            /* Black */
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 400px;
            border: 1px solid #333;
        }

        .login-box h2 {
            color: #ffffff;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 25px;
            /* Tambah spacing (dari 20px jadi 25px) */
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #e0e0e0;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #333;
            background: #2a2a2a;
            color: #ffffff;
            border-radius: 8px;
            font-size: 14px;
            transition: border 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #ff9800;
        }

        /* Sign In Button - Tambah margin */
        .login-box .btn-primary {
            margin-bottom: 25px;
            /* Spacing sebelum lang buttons */
        }

        /* Language Switch di Login - Sama kayak header */
        .login-box .lang-switch {
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .login-box .lang-btn {
            padding: 5px 12px;
            border: 2px solid #ffffff !important;
            background: transparent !important;
            color: #ffffff !important;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .login-box .lang-btn.active,
        .login-box .lang-btn:hover {
            background: #ffffff !important;
            color: #1a1a1a !important;
        }

        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #c62828;
        }

        .success-message {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #2e7d32;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
    @yield('extra-css')
</head>

<body>
    @yield('content')

    @yield('extra-js')
</body>

</html>