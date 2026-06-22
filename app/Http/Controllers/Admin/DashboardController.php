<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Patient, Product, Transaction, MedicalRecord};
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Stat cards
        $totalPasien   = Patient::count();
        $totalProduk   = Product::where('is_active', true)->count();
        $transaksiHari = Transaction::whereDate('created_at', today())
            ->where('status', 'lunas')->count();
        $omzetHari     = Transaction::whereDate('created_at', today())
            ->where('status', 'lunas')->sum('total_bayar');

        // Stok menipis
        $stokMenipis = Product::where('is_active', true)
            ->whereColumn('stok', '<=', 'stok_minimum')
            ->with('category')
            ->get();

        // Grafik omzet 7 hari
        $grafikOmzet = collect();
        for ($i = 6; $i >= 0; $i--) {
            $tgl = now()->subDays($i)->format('Y-m-d');
            $total = Transaction::whereDate('created_at', $tgl)
                ->where('status', 'lunas')
                ->sum('total_bayar');
            $grafikOmzet->put(now()->subDays($i)->format('D'), $total);
        }

        // Transaksi terbaru
        $transaksiTerbaru = Transaction::with(['patient', 'kasir'])
            ->latest()
            ->take(8)
            ->get();

        // Rekam medis terbaru
        $rekamTerbaru = MedicalRecord::with(['patient'])
            ->latest()
            ->take(5)
            ->get();

        // Top produk terjual bulan ini
        $topProduk = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->whereMonth('transactions.created_at', now()->month)
            ->where('transactions.status', 'lunas')
            ->whereNull('transactions.deleted_at')
            ->select('products.nama', DB::raw('SUM(transaction_items.qty) as total_qty'))
            ->groupBy('products.id', 'products.nama')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // Pesanan yang belum diambil (semua, urut dari yang terdekat)
        $pesananDiambil = Transaction::with(['patient', 'items'])
            ->where('diambil', 2)
            ->where('status', '!=', 'batal')
            ->orderBy('tgl_selesai_janji', 'asc')
            ->get();

        return view('admin.dashboard', compact(
            'totalPasien', 'totalProduk', 'transaksiHari', 'omzetHari',
            'stokMenipis', 'grafikOmzet', 'transaksiTerbaru', 'rekamTerbaru', 'topProduk', 'pesananDiambil'
        ));
    }
}
