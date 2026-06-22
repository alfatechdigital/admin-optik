<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Optik Perkasa</title>
    <link rel="icon" href="https://optikperkasa.com/wp-content/uploads/2025/10/cropped-3-3-32x32.png" sizes="32x32" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1e2a5e 0%, #2d4a9e 100%);
            display: flex; align-items: center; justify-content: center;
        }
        .login-card {
            width: 100%; max-width: 420px;
            background: #fff; border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
            overflow: hidden;
        }
        .login-header {
            background: #1e2a5e;
            padding: 32px 40px 28px;
            text-align: center; color: #fff;
        }
        .login-header .logo-icon {
            font-size: 3rem; margin-bottom: 8px;
        }
        .login-header h4 { font-weight: 700; margin: 0; font-size: 1.4rem; }
        .login-header p  { margin: 4px 0 0; opacity: .7; font-size: .85rem; }
        .login-body { padding: 32px 40px; }
        .form-control:focus { border-color: #1e2a5e; box-shadow: 0 0 0 .25rem rgba(30,42,94,.15); }
        .btn-login {
            background: #1e2a5e; color: #fff; border: none;
            padding: 12px; font-weight: 600; letter-spacing: .02em;
            border-radius: 10px; transition: background .2s;
        }
        .btn-login:hover { background: #16235a; color: #fff; }
        .input-group-text { background: #f8f9ff; border-right: none; color: #6c757d; }
        .form-control { border-left: none; }
        .form-control:focus { border-left: none; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="logo-icon"><img src="/icon.png" alt="Optik Perkasa" width="80" height="80"></div>
            <h4>Optik Perkasa</h4>
            <p>Sistem Manajemen Optik</p>
        </div>
        <div class="login-body">
            @if(session('status'))
                <div class="alert alert-success mb-3">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               placeholder="admin@optik.com"
                               required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="••••••••"
                               required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label text-muted small" for="remember">Ingat saya</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-login w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>
            </form>

            <div class="text-center mt-4 text-muted small">
                <i class="bi bi-shield-lock me-1"></i>
                Akses terbatas untuk staff Optik Perkasa
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
