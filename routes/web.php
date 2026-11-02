<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\MedicalRecordController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\PickupController;

Route::get('/', fn() => redirect()->route('dashboard'));

require __DIR__ . '/auth.php';

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware(['permission:patient.view'])->group(
        function () {
            Route::resource('patients', PatientController::class);
            Route::get('patients/search/json', [PatientController::class, 'search'])->name('patients.search');
            Route::get('patients/export/excel', [PatientController::class, 'export'])->name('patients.export');
            Route::post('patients/import-excel', [PatientController::class, 'import'])->name('patients.import');
            Route::get('patients/{patient}/latest-refraction', [PatientController::class, 'latestRefraction'])->name('patients.latest-refraction');
        }
    );

    Route::middleware(['permission:medical_record.view'])->group(
        function () {
            Route::resource('medical-records', MedicalRecordController::class);
            Route::get('api/rekam-medis-detail/{medicalRecord}', [MedicalRecordController::class, 'getDetailJson']);
        }
    );

    // diagnosis routes
    Route::middleware(['permission:medical_record.view'])->group(
        function () {
            Route::resource('diagnoses', DiagnosisController::class);
            Route::get('diagnoses/search/json', [DiagnosisController::class, 'search'])->name('diagnoses.search');
        }
    );

    Route::middleware(['permission:product.view'])->group(
        function () {
            Route::resource('categories', CategoryController::class);
            Route::resource('products', ProductController::class);
            Route::get('products/export/excel', [ProductController::class, 'export'])->name('products.export');
            Route::post('products/import-excel', [ProductController::class, 'import'])->name('products.import');
        }
    );

    Route::middleware(['permission:transaction.view'])->group(
        function () {
            Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
            Route::get('transactions/create/{transaction?}', [TransactionController::class, 'create'])->name('transactions.create');
            Route::post('transactions', [TransactionController::class, 'store'])->name('transactions.store');
            Route::get('transactions/product/search', [TransactionController::class, 'searchProduct'])->name('transactions.product.search');
            Route::get('transactions/get-medical-records', [TransactionController::class, 'getMedicalRecords'])->name('transactions.medical-records');
            Route::get('medical-records/by-patient/{patient_id}', [MedicalRecordController::class, 'getByPatient'])->name('medical-records.by-patient');
            // --- ADVANCED POS ROUTES ---
            Route::get('transactions/pos/nav', [TransactionController::class, 'posNav'])->name('transactions.pos.nav');
            Route::get('transactions/pos/search', [TransactionController::class, 'posSearch'])->name('transactions.pos.search');
            Route::post('transactions/pos/save', [TransactionController::class, 'posSave'])->name('transactions.pos.save');
            Route::delete('transactions/pos/delete/{id}', [TransactionController::class, 'posDelete'])->name('transactions.pos.delete');
            Route::get('transactions/pos/patient-autocomplete', [TransactionController::class, 'patientAutocomplete'])->name('patients.autocomplete');
            Route::get('transactions/pos/frame-autocomplete', [TransactionController::class, 'frameAutocomplete'])->name('products.frame.autocomplete');
            Route::get('transactions/pos/doctor-autocomplete', [MedicalRecordController::class, 'doctorAutocomplete'])->name('doctors.autocomplete');
            // ---------------------------
    
            // Pickup Transaction
            Route::get('transactions/pickup', [PickupController::class, 'index'])->name('pickup.index');
            Route::get('transactions/pickup/{transaction}', [PickupController::class, 'pickupShow'])->name('pickup.show');
            Route::post('/pickup/{transaction}/pay', [PickupController::class, 'store'])->name('pickup.store');
            Route::patch('/pickup/{transaction}/confirm', [PickupController::class, 'confirm'])->name('pickup.confirm');

            Route::get('transactions/{transaction}/print', [TransactionController::class, 'printView'])->name('transactions.print');
            Route::get('transactions/{transaction}/download-bpjs', [TransactionController::class, 'downloadBpjs'])->name('transactions.download_bpjs');
            Route::get('transactions/{transaction}/download-nota-besar', [TransactionController::class, 'downloadNotaBesar'])->name('transactions.download_nota_besar');
            Route::get('transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
        }
    );

    Route::middleware(['permission:transaction.edit'])->group(
        function () {
            Route::get('transactions/{transaction}/edit', [TransactionController::class, 'create'])->name('transactions.edit');
            Route::delete('transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
            Route::post('transactions/{transaction}/markup', [TransactionController::class, 'saveMarkup'])->name('transactions.markup');
        }
    );

    Route::middleware(['permission:report.view'])->prefix('reports')->name('reports.')->group(
        function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/penjualan', [ReportController::class, 'penjualan'])->name('penjualan');
            Route::get('/produk-terlaris', [ReportController::class, 'produkTerlaris'])->name('produk-terlaris');
            Route::get('/stok', [ReportController::class, 'stok'])->name('stok');
        }
    );

    Route::middleware(['role:super_admin'])->group(
        function () {
            Route::resource('users', UserController::class);
        }
    );
});
