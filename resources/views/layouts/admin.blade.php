<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Optik Perkasa</title>
    <link rel="icon" href="https://optikperkasa.com/wp-content/uploads/2025/10/cropped-3-3-32x32.png" sizes="32x32" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #1e2a5e;
            --sidebar-hover: rgba(255, 255, 255, .1);
            --sidebar-active: rgba(255, 255, 255, .18);
            --topbar-height: 60px;
        }

        body {
            background: #f4f6fb;
            font-size: .9rem;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 1040;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 18px 20px;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 600;
            border-bottom: 1px solid rgba(255, 255, 255, .1);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-brand:hover {
            color: #fff;
        }

        .nav-section {
            padding: 14px 20px 4px;
            font-size: .7rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .4);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, .75);
            padding: 8px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 8px;
            margin: 2px 10px;
            font-size: .85rem;
            transition: background .15s, color .15s;
        }

        .sidebar .nav-link:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }

        .sidebar .nav-link.active {
            background: var(--sidebar-active);
            color: #fff;
            font-weight: 500;
        }

        .sidebar .nav-link .bi {
            font-size: 1rem;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 16px;
            border-top: 1px solid rgba(255, 255, 255, .1);
        }

        /* Main content */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            height: var(--topbar-height);
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .topbar-title {
            font-weight: 600;
            font-size: 1rem;
            flex: 1;
        }

        .content {
            padding: 24px;
            flex: 1;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 8px rgba(0, 0, 0, .07);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #f0f0f0;
            font-weight: 600;
        }

        /* Stat cards */
        .stat-card {
            border-left: 4px solid;
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .stat-val {
            font-size: 1.6rem;
            font-weight: 700;
            line-height: 1.1;
        }

        .stat-label {
            font-size: .78rem;
            color: #6c757d;
            margin-bottom: 2px;
        }

        /* Table */
        .table th {
            font-size: .78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #6c757d;
            border-top: none;
        }

        .table td {
            vertical-align: middle;
        }

        /* Badge status */
        .badge-lunas {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-batal {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Responsive */
        /* Offline Banner */
        #offline-banner {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: #dc3545;
            color: #fff;
            text-align: center;
            padding: 8px;
            z-index: 9999;
            font-weight: 600;
            font-size: 0.85rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Loading Spinner */
        .spinner-submit {
            width: 1rem;
            height: 1rem;
            border-width: 0.15em;
            margin-right: 8px;
            display: none;
        }

        .btn-loading .spinner-submit {
            display: inline-block;
        }

        /* Validation Error Highlighting */
        .is-invalid+.invalid-feedback {
            display: block;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform .3s;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <div id="offline-banner">
        <i class="bi bi-wifi-off me-2"></i> Koneksi internet terputus. Beberapa fitur mungkin tidak berfungsi.
    </div>

    @php
        $totalPendingPickups = \App\Models\Transaction::where('diambil', 2)
            ->where('status', '!=', 'batal')
            ->count();

        // Notifikasi: Yang belum diambil AND (Tanggal <= Besok OR Tanggal Kosong)
        $pickupNotifQuery = \App\Models\Transaction::where('diambil', 2)
            ->where('status', '!=', 'batal')
            ->where(function ($q) {
                $q->whereDate('tgl_selesai_janji', '<=', now()->addDay()->toDateString())
                    ->orWhereNull('tgl_selesai_janji');
            });

        $pickupTodayCount = $pickupNotifQuery->count();

        $allPickupsNotif = $pickupNotifQuery->with('patient', 'items')
            ->orderByRaw('tgl_selesai_janji IS NULL ASC') // NULL at the end
            ->orderBy('tgl_selesai_janji', 'asc')
            ->take(50)
            ->get();
    @endphp

    {{-- SIDEBAR --}}
    <nav class="sidebar" id="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <img src="/icon.png" alt="Optik Perkasa" width="40" height="40">
            <span>Optik Perkasa</span>
        </a>

        <div class="nav-section">Menu</div>

        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="nav-section">Pasien</div>

        <a href="{{ route('patients.index') }}" class="nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Data Pasien
        </a>

        @canany(['medical_record.view'])
            <a href="{{ route('medical-records.index') }}"
                class="nav-link {{ request()->routeIs('medical-records.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard2-pulse"></i> Rekam Medis
            </a>
        @endcanany

        <div class="nav-section">Toko</div>

        <!-- Parent Menu -->
        <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#posMenu"
            role="button" aria-expanded="{{ request()->routeIs('transactions.*') ? 'true' : 'false' }}">
            <span><i class="bi bi-cart-plus"></i> POS / Kasir</span>
            <i class="bi bi-chevron-down"></i>
        </a>
        <a href="{{ route('transactions.create') }}"
            class="nav-link ms-3 {{ request()->routeIs('transactions.create') ? 'active' : '' }}">
            <i class="bi bi-plus-circle"></i> Transaksi
        </a>

        <!-- Sub Menu -->
        <!-- <div class="collapse {{ request()->routeIs('transactions.*') ? 'show' : '' }}" id="posMenu">

            <a href="{{ route('transactions.create') }}"
                class="nav-link ms-3 {{ request()->routeIs('transactions.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i> Transaksi
            </a>

            <a href="{{ route('pickup.index') }}"
                class="nav-link ms-3 {{ request()->routeIs('pickup.index') ? 'active' : '' }}">
                <i class="bi bi-eyeglasses"></i> Pengambilan Kacamata
            </a>

        </div> -->

        <a href="{{ route('transactions.index') }}"
            class="nav-link {{ request()->routeIs('transactions.index', 'transactions.show') ? 'active' : '' }}">
            <i class="bi bi-receipt-cutoff"></i> Riwayat Transaksi
        </a>

        @canany(['product.view'])
            <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Produk
            </a>

            <a href="{{ route('categories.index') }}"
                class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i> Kategori
            </a>
        @endcanany

        @canany(['report.view'])
            <div class="nav-section">Laporan</div>

            <a href="{{ route('reports.penjualan') }}"
                class="nav-link {{ request()->routeIs('reports.penjualan') ? 'active' : '' }}">
                <i class="bi bi-graph-up-arrow"></i> Lap. Penjualan
            </a>

            <!-- <a href="{{ route('reports.produk-terlaris') }}"
                            class="nav-link {{ request()->routeIs('reports.produk-terlaris') ? 'active' : '' }}">
                            <i class="bi bi-trophy"></i> Produk Terlaris
                        </a>

                        <a href="{{ route('reports.stok') }}" class="nav-link {{ request()->routeIs('reports.stok') ? 'active' : '' }}">
                            <i class="bi bi-boxes"></i> Lap. Stok
                        </a> -->
        @endcanany

        @if(auth()->user()->hasRole('super_admin'))
            <div class="nav-section">Sistem</div>

            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i> Manajemen User
            </a>
        @endif

        <div class="sidebar-footer">
            <div class="text-white-50 small mb-2">
                <i class="bi bi-person-circle me-1"></i>
                {{ auth()->user()->name }}
                <span class="badge bg-secondary ms-1" style="font-size:.65rem">
                    {{ auth()->user()->getRoleNames()->first() }}
                </span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-outline-light btn-sm w-100">
                    <i class="bi bi-box-arrow-left me-1"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    {{-- MAIN --}}
    <div class="main-wrapper">
        {{-- TOPBAR --}}
        <div class="topbar">
            <button class="btn btn-sm btn-light d-md-none"
                onclick="document.getElementById('sidebar').classList.toggle('show')">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>

            <div class="d-flex align-items-center me-3 dropdown">
                <button class="btn btn-light position-relative" type="button" id="notifDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false" title="Pesanan Siap Diambil & H-1">
                    <i class="bi bi-bell fs-5 text-secondary"></i>
                    @if($pickupTodayCount > 0)
                        <span
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light">
                            {{ $pickupTodayCount }}
                            <span class="visually-hidden">unread messages</span>
                        </span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notifDropdown"
                    style="width: 320px; max-height: 400px; overflow-y: auto;">
                    <div class="dropdown-header fw-bold border-bottom pb-2 pt-1 d-flex justify-content-between align-items-center bg-white position-sticky top-0"
                        style="z-index: 10;">
                        <span class="text-dark">Notifikasi Pengambilan & H-1</span>
                        <a href="{{ route('pickup.index') }}" class="text-decoration-none small text-primary">Lihat
                            Semua</a>
                    </div>
                    @forelse($allPickupsNotif as $trx)
                        @php
                            $dt = $trx->tgl_selesai_janji ? \Carbon\Carbon::parse($trx->tgl_selesai_janji) : null;
                            $isPast = $dt ? $dt->isPast() && !$dt->isToday() : false;
                            $isToday = $dt ? $dt->isToday() : false;
                            $isTomorrow = $dt ? $dt->isTomorrow() : false;

                            $dateClass = $isPast ? 'text-danger fw-bold' : ($isToday ? 'text-warning fw-bold' : ($isTomorrow ? 'text-primary fw-bold' : 'text-muted'));
                            $dateLabel = $isPast ? 'Terlewat' : ($isToday ? 'Hari Ini' : ($isTomorrow ? 'Besok (H-1)' : ($dt ? '' : 'Belum Ada Jadwal')));
                        @endphp
                        <a href="{{ route('transactions.show', $trx) }}" class="dropdown-item py-2 border-bottom text-wrap">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-semibold text-truncate"
                                    style="font-size: 0.85rem; max-width: 60%;">{{ $trx->patient->nama ?? ($trx->nama_pasien ?? 'Umum') }}</span>
                                <span class="{{ $dateClass }}" style="font-size: 0.72rem;">
                                    {{ $dt ? $dt->format('d/m/Y') : 'Tgl: -' }}
                                    @if($dateLabel)
                                        <span
                                            class="badge {{ $isPast ? 'bg-danger' : ($isToday ? 'bg-warning text-dark' : ($isTomorrow ? 'bg-primary' : 'bg-secondary')) }}"
                                            style="font-size: 0.6rem;">{{ $dateLabel }}</span>
                                    @endif
                                </span>
                            </div>
                            <div style="font-size: 0.75rem; color: #555;">
                                @foreach($trx->items as $idx => $item)
                                    {{ $item->qty }}x {{ $item->nama_produk }}@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        </a>
                    @empty
                        <div class="p-3 text-center text-muted small">
                            Tidak ada pesanan yang perlu diambil.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="text-muted small d-none d-md-block">
                {{ now()->isoFormat('dddd, D MMMM Y') }}
            </div>
        </div>

        {{-- CONTENT --}}
        <div class="content">
            {{-- Alert messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3"
                    role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3"
                    role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Terdapat kesalahan input:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Global AJAX CSRF Setup
        document.addEventListener('DOMContentLoaded', function () {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // For jQuery if present
            if (window.jQuery) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                });
            }

            // For Axios if present (standard Laravel behavior, but ensure it's set)
            if (window.axios) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
            }
        });

        // Global Autosave Feature
        document.addEventListener('DOMContentLoaded', function () {
            const forms = document.querySelectorAll('form[method="POST"]:not([data-no-autosave="true"])');

            forms.forEach(form => {
                // Ignore logout forms
                if (form.action && form.action.includes('logout')) return;

                const urlPath = window.location.pathname;
                const formId = form.id || form.name || 'default';
                const storageKey = `autosave_${urlPath}_${formId}`;

                // Restore data if exists
                const savedDataStr = localStorage.getItem(storageKey);
                if (savedDataStr) {
                    Swal.fire({
                        title: 'Draft Tersedia',
                        text: 'Ada data isian yang belum tersimpan pada halaman ini. Apakah Anda ingin memuat ulang draft tersebut atau mulai dari awal?',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'Muat Ulang Draft',
                        cancelButtonText: 'Mulai dari Awal',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            try {
                                const savedData = JSON.parse(savedDataStr);
                                let hasRestored = false;

                                Object.keys(savedData).forEach(name => {
                                    if (['password', 'password_confirmation', '_token', '_method'].includes(name)) return;

                                    const value = savedData[name];
                                    const elements = form.querySelectorAll(`[name="${name}"]`);

                                    elements.forEach(el => {
                                        if (el.type === 'file') return;

                                        if (el.type === 'radio' || el.type === 'checkbox') {
                                            if (el.value === value || (Array.isArray(value) && value.includes(el.value))) {
                                                el.checked = true;
                                                hasRestored = true;
                                            }
                                        } else {
                                            el.value = value;
                                            hasRestored = true;
                                        }

                                        // Trigger events so listeners can react
                                        el.dispatchEvent(new Event('input', { bubbles: true }));
                                        el.dispatchEvent(new Event('change', { bubbles: true }));
                                    });
                                });

                                if (hasRestored) {
                                    form.dispatchEvent(new CustomEvent('autosaveRestored', { detail: savedData }));
                                }
                            } catch (e) {
                                console.error('Error parsing autosave data', e);
                            }
                        } else {
                            localStorage.removeItem(storageKey);
                        }
                    });
                }

                // ==========================================================
                // MODIFIKASI AUTOSAVE: Hanya Simpan Jika Sudah Masuk Step 2 dst.
                // ==========================================================
                const saveFormData = () => {
                    // Batasi agar Step 1 TIDAK MENYIMPAN data apa pun ke localstorage
                    if (typeof currentStep !== 'undefined' && currentStep < 2) {
                        return;
                    }

                    const formData = new FormData(form);
                    const dataObj = {};

                    for (let [key, value] of formData.entries()) {
                        if (['password', 'password_confirmation', '_token', '_method'].includes(key)) continue;

                        const element = form.querySelector(`[name="${key}"]`);
                        if (element && element.type === 'file') continue;

                        if (dataObj.hasOwnProperty(key)) {
                            if (!Array.isArray(dataObj[key])) {
                                dataObj[key] = [dataObj[key]];
                            }
                            dataObj[key].push(value);
                        } else {
                            dataObj[key] = value;
                        }
                    }

                    localStorage.setItem(storageKey, JSON.stringify(dataObj));
                };

                form.addEventListener('input', saveFormData);
                form.addEventListener('change', saveFormData);

                // Bersihkan draft saat form berhasil disimpan (Submit)
                form.addEventListener('submit', function () {
                    localStorage.removeItem(storageKey);
                });
            });
        });

        // Offline / Online Detection
        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);

        function updateOnlineStatus() {
            const banner = document.getElementById('offline-banner');
            if (banner) {
                if (navigator.onLine) banner.style.display = 'none';
                else banner.style.display = 'block';
            }
        }
        updateOnlineStatus();

        // Global Loading State for Forms
        document.addEventListener('submit', function (e) {
            if (e.defaultPrevented) return;

            const form = e.target;
            if (form.tagName === 'FORM' && !form.hasAttribute('data-no-loading')) {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    if (submitBtn.classList.contains('btn-loading')) {
                        e.preventDefault();
                        return;
                    }

                    submitBtn.classList.add('btn-loading');
                    submitBtn.disabled = true;

                    if (!submitBtn.querySelector('.spinner-submit')) {
                        const spinner = document.createElement('span');
                        spinner.className = 'spinner-border spinner-submit';
                        spinner.setAttribute('role', 'status');
                        spinner.setAttribute('aria-hidden', 'true');
                        submitBtn.prepend(spinner);
                    }
                }
            }
        });

        // Dirty Form Check
        let isDirty = false;
        const markDirty = () => { isDirty = true; };

        document.addEventListener('change', (e) => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT' || e.target.tagName === 'TEXTAREA') {
                markDirty();
            }
        });

        window.addEventListener('beforeunload', (e) => {
            if (isDirty) {
                e.preventDefault();
                e.returnValue = 'Anda memiliki perubahan yang belum disimpan. Yakin ingin keluar?';
            }
        });

        document.addEventListener('submit', () => {
            isDirty = false;
        });

        function handleValidationErrors(errors, container = document) {
            container.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            container.querySelectorAll('.invalid-feedback.ajax-error').forEach(el => el.remove());

            Object.keys(errors).forEach(field => {
                const messages = errors[field];
                let input = container.querySelector(`[name="${field}"]`);

                if (!input) {
                    const selector = field.replace(/\./g, '\\.');
                    input = container.querySelector(`[name="${selector}"]`);
                }

                if (input) {
                    input.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback ajax-error';
                    feedback.innerText = messages[0];
                    input.parentNode.insertBefore(feedback, input.nextSibling);
                }
            });
        }

        if (window.axios) {
            window.axios.interceptors.response.use(
                response => response,
                error => {
                    if (error.response && error.response.status === 422) {
                        handleValidationErrors(error.response.data.errors);
                    }
                    return Promise.reject(error);
                }
            );
        }

        if (window.jQuery) {
            $(document).ajaxError(function (event, jqXHR, ajaxSettings, thrownError) {
                if (jqXHR.status === 422) {
                    const response = jqXHR.responseJSON;
                    if (response && response.errors) {
                        handleValidationErrors(response.errors);
                    }
                }
            });
        }
    </script>
    @stack('scripts')
</body>

</html>