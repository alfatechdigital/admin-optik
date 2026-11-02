<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Transaction, Product, TransactionItem};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function penjualan(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->format('Y-m-d');
        $to   = $request->to   ?? now()->format('Y-m-d');

        $query = Transaction::with(['patient', 'kasir', 'items'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$from, $to])
            ->where('status', 'lunas');

        if ($request->jenis == 'bpjs') {
            $query->where('typefaktur', 2);
        } elseif ($request->jenis == 'umum') {
            $query->where('typefaktur', 1);
        }

        $transactions = $query->latest()->get();

        $summary = [
            'total_transaksi' => $transactions->count(),
            'total_omzet'     => $transactions->sum('total_bayar'),
            'total_diskon'    => $transactions->sum('potongan'),
            'total_potongan_bpjs' => $transactions->sum('potongan_bpjs'),
            'rata_per_trx'    => $transactions->count()
                ? round($transactions->sum('total_bayar') / $transactions->count())
                : 0,
        ];

        // Grafik per hari
        $grafik = $transactions->groupBy(fn($t) => $t->created_at->format('d/m'))
            ->map(fn($g) => $g->sum('total_bayar'));

        return view('admin.reports.penjualan', compact('transactions', 'summary', 'from', 'to', 'grafik'));
    }

    public function produkTerlaris(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->format('Y-m-d');
        $to   = $request->to   ?? now()->format('Y-m-d');

        $products = TransactionItem::with('product.category')
            ->whereHas('transaction', fn($q) =>
                $q->whereBetween(DB::raw('DATE(created_at)'), [$from, $to])
                  ->where('status', 'lunas')
            )
            ->select(
                'product_id',
                DB::raw('SUM(qty) as total_terjual'),
                DB::raw('SUM(subtotal) as total_pendapatan')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_terjual')
            ->take(15)
            ->get();

        return view('admin.reports.produk-terlaris', compact('products', 'from', 'to'));
    }

    public function stok()
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('stok')
            ->get();

        $summary = [
            'total_produk'  => $products->count(),
            'stok_menipis'  => $products->filter(fn($p) => $p->stok <= $p->stok_minimum)->count(),
            'stok_habis'    => $products->where('stok', 0)->count(),
            'nilai_stok'    => $products->sum(fn($p) => $p->stok * $p->harga_beli),
        ];

        return view('admin.reports.stok', compact('products', 'summary'));
    }
}
