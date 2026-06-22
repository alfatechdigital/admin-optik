<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — Optik Perkasa</title>
    <link rel="icon" href="https://optikperkasa.com/wp-content/uploads/2025/10/cropped-3-3-32x32.png" sizes="32x32" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6fb;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        .error-card {
            background: #fff;
            border-radius: 24px;
            padding: 48px;
            box-shadow: 0 20px 60px rgba(30, 42, 94, 0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .error-icon {
            font-size: 5rem;
            color: #1e2a5e;
            margin-bottom: 24px;
        }
        .error-code {
            font-size: 1.2rem;
            font-weight: 700;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 8px;
        }
        .error-title {
            font-size: 2rem;
            font-weight: 800;
            color: #1e2a5e;
            margin-bottom: 16px;
        }
        .error-msg {
            color: #6c757d;
            margin-bottom: 32px;
            line-height: 1.6;
        }
        .btn-back {
            background: #1e2a5e;
            color: #fff;
            border: none;
            padding: 12px 32px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-back:hover {
            background: #16235a;
            color: #fff;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon">
            @yield('icon')
        </div>
        <div class="error-code">@yield('code')</div>
        <h1 class="error-title">@yield('title')</h1>
        <p class="error-msg">@yield('message')</p>
        <a href="{{ url('/') }}" class="btn btn-back">
            <i class="bi bi-house-door"></i> Kembali ke Dashboard
        </a>
    </div>
</body>
</html>
