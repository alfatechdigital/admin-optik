@extends('layouts.admin')

@section('title', 'Point of Sale — Wizard')
@section('page-title', '')

@push('styles')
    <style>
        /* =============================================
                   ROOT & BASE
                ============================================= */
        :root {
            --primary: #0d6efd;
            --primary-dark: #0b5ed7;
            --success: #198754;
            --danger: #dc3545;
            --warning: #ffc107;
            --glass-bg: rgba(255, 255, 255, 0.75);
            --glass-border: rgba(255, 255, 255, 0.35);
            --gradient-blue: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
            --shadow-soft: 0 8px 32px rgba(31, 38, 135, 0.08);
            --radius-card: 18px;
            --radius-input: 10px;
        }

        body {
            background: #f0f2f5;
            font-family: 'Inter', system-ui, sans-serif;
        }

        /* =============================================
                   WIZARD & UI (Rest of CSS)
                ============================================= */
        .wizard-steps {
            display: flex;
            align-items: center;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-card);
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-soft);
            overflow-x: auto;
            gap: 0;
        }

        .wizard-step {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            min-width: 130px;
            padding: 8px 10px;
            border-radius: 12px;
            cursor: default;
            transition: background 0.2s;
            user-select: none;
        }

        .wizard-step.is-done {
            cursor: pointer;
        }

        .wizard-step.is-done:hover {
            background: rgba(13, 110, 253, 0.06);
        }

        .ws-num {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
            transition: all 0.3s;
            border: 2px solid #dee2e6;
            background: #fff;
            color: #adb5bd;
        }

        .ws-text {
            line-height: 1.2;
        }

        .ws-title {
            font-size: 13px;
            font-weight: 600;
            color: #6c757d;
            transition: color 0.2s;
        }

        .ws-sub {
            font-size: 11px;
            color: #adb5bd;
        }

        .wizard-step.is-active .ws-num {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.35);
        }

        .wizard-step.is-active .ws-title {
            color: #212529;
        }

        .wizard-step.is-active .ws-sub {
            color: #6c757d;
        }

        .wizard-step.is-done .ws-num {
            background: var(--success);
            border-color: var(--success);
            color: #fff;
        }

        .wizard-step.is-done .ws-title {
            color: var(--success);
        }

        .ws-connector {
            flex: 0 0 28px;
            height: 2px;
            background: #dee2e6;
            transition: background 0.3s;
            border-radius: 2px;
            margin: 0 2px;
        }

        .ws-connector.is-done {
            background: var(--success);
        }

        .wizard-panel {
            display: none;
            animation: fadeSlide 0.25s ease;
        }

        .wizard-panel.is-active {
            display: block;
        }

        @keyframes fadeSlide {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-soft);
            margin-bottom: 1.25rem;
        }

        .glass-card:last-child {
            margin-bottom: 0;
        }

        .card-header-section {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-header-section i {
            font-size: 1rem;
        }

        .badge-opt {
            font-size: 0.65rem;
            background: #f1f3f5;
            color: #6c757d;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 500;
        }

        .badge-req {
            font-size: 0.65rem;
            background: #fff3cd;
            color: #856404;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 500;
        }

        .badge-auto {
            font-size: 0.65rem;
            background: #d1e7dd;
            color: #0a3622;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 500;
        }

        .form-label {
            font-size: 0.72rem;
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 4px;
        }

        .form-control-sm,
        .form-select-sm {
            border-radius: var(--radius-input);
            border: 1px solid #dee2e6;
            padding: 0.45rem 0.7rem;
            background: rgba(255, 255, 255, 0.9);
            font-size: 0.82rem;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .form-control-sm:focus,
        .form-select-sm:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12);
            outline: none;
        }

        .form-control-sm.is-invalid {
            border-color: var(--danger);
        }

        .form-control-sm.is-valid {
            border-color: var(--success);
        }

        .invalid-feedback {
            font-size: 0.7rem;
        }

        .refraction-grid {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #eee;
            overflow: hidden;
        }

        .refraction-grid table {
            margin-bottom: 0;
        }

        .refraction-grid thead th {
            background: #f8f9fa;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #495057;
            padding: 8px 6px;
            text-align: center;
            border-bottom: 2px solid #dee2e6;
        }

        .refraction-grid thead th:first-child {
            text-align: left;
            padding-left: 12px;
        }

        .refraction-grid tbody td {
            padding: 6px 4px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }

        .refraction-grid tbody td:first-child {
            padding-left: 12px;
            font-weight: 700;
            font-size: 0.8rem;
        }

        .refraction-grid input {
            border: none;
            background: #f8f9fa;
            border-radius: 8px;
            text-align: center;
            padding: 4px 6px;
            font-size: 0.82rem;
            width: 100%;
            min-width: 52px;
            transition: background 0.15s;
        }

        .refraction-grid input:focus {
            outline: none;
            background: #e8f0fe;
        }

        .cart-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-soft);
            position: sticky;
            top: 4rem;
        }

        .cart-item-row {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .cart-item-row:last-child {
            border-bottom: none;
        }

        .cart-type-badge {
            font-size: 0.62rem;
            padding: 2px 7px;
            border-radius: 20px;
            font-weight: 600;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .ct-frame {
            background: #ede9fe;
            color: #5b21b6;
        }

        .ct-lensa {
            background: #d1fae5;
            color: #065f46;
        }

        .ct-other {
            background: #f3f4f6;
            color: #374151;
        }

        .cart-item-name {
            flex: 1;
            font-size: 0.78rem;
            color: #ffffffff;
            min-width: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cart-item-qty {
            width: 42px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            font-size: 0.78rem;
            padding: 3px 0;
            background: #fff;
        }

        .cart-item-price {
            font-size: 0.78rem;
            font-weight: 600;
            color: #212529;
            white-space: nowrap;
            min-width: 72px;
            text-align: right;
        }

        .cart-item-del {
            color: #adb5bd;
            cursor: pointer;
            font-size: 0.75rem;
            flex-shrink: 0;
            transition: color 0.15s;
        }

        .cart-item-del:hover {
            color: var(--danger);
        }

        .cart-total-section {
            border-top: 2px solid rgba(0, 0, 0, 0.07);
            margin-top: 8px;
            padding-top: 8px;
        }

        #cart-inline-body {
            max-height: 380px;
            overflow-y: auto;
            padding-right: 4px;
        }

        #cart-inline-body::-webkit-scrollbar {
            width: 4px;
        }

        #cart-inline-body::-webkit-scrollbar-thumb {
            background: #dee2e6;
            border-radius: 4px;
        }

        .cart-item-details {
            font-size: 0.68rem;
            color: #6c757d;
            margin-top: -2px;
        }

        .cart-empty {
            text-align: center;
            padding: 2rem 0;
            color: #adb5bd;
            font-size: 0.8rem;
        }

        .product-type-tabs {
            display: flex;
            gap: 6px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .ptype-btn {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.73rem;
            font-weight: 600;
            border: 1.5px solid #dee2e6;
            background: #fff;
            color: #6c757d;
            cursor: pointer;
            transition: all 0.15s;
        }

        .ptype-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .ptype-btn.active-frame {
            border-color: #7c3aed;
            background: #ede9fe;
            color: #5b21b6;
        }

        .ptype-btn.active-lensa {
            border-color: #059669;
            background: #d1fae5;
            color: #065f46;
        }

        .ptype-btn.active-other {
            border-color: #374151;
            background: #f3f4f6;
            color: #374151;
        }

        .finance-card {
            background: var(--gradient-blue);
            border-radius: var(--radius-card);
            padding: 1.5rem;
            color: #fff;
        }

        .finance-card .form-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.72rem;
        }

        .finance-card .form-control {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #fff;
            border-radius: var(--radius-input);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .finance-card .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .finance-card .form-control:focus {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: none;
        }

        .sisa-field {
            background: rgba(220, 53, 69, 0.25) !important;
            border-color: rgba(220, 53, 69, 0.5) !important;
            font-size: 1.1rem !important;
        }

        .finance-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.72rem;
            font-weight: 600;
            display: block;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-box {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 16px;
        }

        .summary-box .s-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            padding: 3px 0;
        }

        .summary-box .s-label {
            color: rgba(255, 255, 255, 0.75);
        }

        .summary-box .s-val {
            font-weight: 600;
            color: #fff;
        }

        .summary-box .s-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin: 6px 0;
        }

        .wizard-nav-bar {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-card);
            padding: 1rem 1.5rem;
            box-shadow: var(--shadow-soft);
            margin-top: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .wizard-nav-bar .step-hint {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .wizard-nav-bar .nav-right {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .global-action-bar {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-card);
            padding: 0.75rem 1.25rem;
            box-shadow: var(--shadow-soft);
            margin-bottom: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            justify-content: space-between;
        }

        .global-action-bar .trx-info {
            font-size: 0.78rem;
            color: #6c757d;
        }

        .global-action-bar .trx-info strong {
            color: var(--primary);
        }

        .btn-action {
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.78rem;
            padding: 0.5rem 1rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s;
            border: 1px solid transparent;
        }

        .btn-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .btn-action:active {
            transform: translateY(0);
        }

        .ac-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1050;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 0 0 10px 10px;
            max-height: 200px;
            overflow-y: auto;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .ac-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f8f8f8;
            font-size: 0.78rem;
        }

        .ac-item:hover {
            background: #f0f4ff;
            color: var(--primary);
        }

        .ac-item:last-child {
            border-bottom: none;
        }

        .step-error-msg {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.78rem;
            color: #664d03;
            margin-bottom: 12px;
            display: none;
            align-items: flex-start;
            gap: 8px;
        }

        .step-error-msg.show {
            display: flex;
        }

        .history-tag {
            font-size: 0.67rem;
            background: #e8f0fe;
            color: #1a56db;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 500;
        }

        @media (max-width: 767px) {
            .wizard-steps {
                padding: 0.75rem 1rem;
            }

            .ws-sub {
                display: none;
            }

            .wizard-nav-bar {
                padding: 0.75rem 1rem;
            }
        }

        #printFrame {
            display: none;
        }

        .co-item-row {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.8rem;
        }

        .co-item-row:last-child {
            border-bottom: none;
        }

        .co-item-name {
            flex: 1;
            color: rgba(255, 255, 255, 0.9);
        }

        .co-item-qty {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.72rem;
        }

        .co-item-price {
            font-weight: 600;
            color: #fff;
        }

        .btn-xs {
            padding: 3px 8px;
            font-size: .75rem;
        }
    </style>
@endpush

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <form action="{{ route('transactions.pos.save') }}" method="POST" id="pos-form">
                @csrf
                <input type="hidden" name="id" id="trx_id">
                <input type="hidden" name="patient_id" id="patient_id">
                <input type="hidden" name="cart_data" id="cart_data" value="[]">

                {{-- =============================================
                GLOBAL ACTION BAR
                ============================================= --}}
                <div class="global-action-bar">
                    <div class="trx-info">
                        <strong id="display-no-trx">—</strong>
                        <span class="mx-2">|</span>
                        <span id="display-step-label">Step 1: Transaksi</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-action btn-secondary shadow-sm" onclick="openSearchModal()"><i
                                class="bi bi-search"></i> Cari</button>
                    </div>
                </div>

                {{-- =============================================
                STEP INDICATOR
                ============================================= --}}
                <div class="wizard-steps" id="wizardStepIndicator">
                    <div class="wizard-step is-active" id="step-tab-1" onclick="tryGoStep(1)">
                        <div class="ws-num">1</div>
                        <div class="ws-text">
                            <div class="ws-title">Transaksi</div>
                            <div class="ws-sub">No faktur &amp; tanggal</div>
                        </div>
                    </div>
                    <div class="ws-connector" id="conn-1"></div>
                    <div class="wizard-step" id="step-tab-2" onclick="tryGoStep(2)">
                        <div class="ws-num">2</div>
                        <div class="ws-text">
                            <div class="ws-title">Pasien &amp; Resep</div>
                            <div class="ws-sub">Data + refraksi</div>
                        </div>
                    </div>
                    <div class="ws-connector" id="conn-2"></div>
                    <div class="wizard-step" id="step-tab-3" onclick="tryGoStep(3)">
                        <div class="ws-num">3</div>
                        <div class="ws-text">
                            <div class="ws-title">Produk</div>
                            <div class="ws-sub">Frame &amp; lensa</div>
                        </div>
                    </div>
                    <div class="ws-connector" id="conn-3"></div>
                    <div class="wizard-step" id="step-tab-4" onclick="tryGoStep(4)">
                        <div class="ws-num">4</div>
                        <div class="ws-text">
                            <div class="ws-title">Data Tambahan</div>
                            <div class="ws-sub">Data lainnya</div>
                        </div>
                    </div>
                    <div class="ws-connector" id="conn-4"></div>
                    <div class="wizard-step" id="step-tab-5" onclick="tryGoStep(5)">
                        <div class="ws-num">5</div>
                        <div class="ws-text">
                            <div class="ws-title">Checkout</div>
                            <div class="ws-sub">Preview Transaksi</div>
                        </div>
                    </div>
                </div>

                {{-- =============================================
                WIZARD PARTIALS
                ============================================= --}}
                @include('admin.transactions.partials.step1-transaction')
                @include('admin.transactions.partials.step2-patient')
                @include('admin.transactions.partials.step3-product')
                @include('admin.transactions.partials.step4-added-data')
                @include('admin.transactions.partials.step5-checkout')

            </form>
        </div>
    </div>

    {{-- =============================================
    MODALS
    ============================================= --}}
    <!-- Search Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:18px;backdrop-filter:blur(16px);">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title card-header-section mb-0"><i class="bi bi-search"></i> Cari Riwayat Transaksi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="modalSearchInput"
                        class="form-control form-control-lg mb-3 shadow-sm rounded-pill"
                        placeholder="Ketik No Transaksi, Nama, atau BPJS..." autocomplete="off">
                    <div class="table-responsive rounded-3 border" style="max-height: 350px;">
                        <table class="table table-hover align-middle mb-0" id="searchTable">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Tgl</th>
                                    <th>No Faktur</th>
                                    <th>Pasien</th>
                                    <th class="text-end">Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3" id="searchPagination">
                        <!-- Pagination will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.transactions.partials.modal-print')

@endsection

@push('scripts')
    @include('admin.transactions.partials.script-pos')
@endpush