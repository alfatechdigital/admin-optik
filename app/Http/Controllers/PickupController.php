<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class PickupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
       $step = (int) $request->get('step', 1);
        $search = $request->get('search');
        $transactionId = $request->get('transaction_id');

        $results = null;
        $transaction = null;

        /**
         * =========================
         * STEP 1 — SEARCH
         * =========================
         */
        if ($step === 1 && $search) {
            $results = Transaction::with('patient')
                ->where(function ($q) use ($search) {
                    $q->where('no_transaksi', 'like', "%{$search}%")
                      ->orWhereHas('patient', function ($q2) use ($search) {
                          $q2->where('nama', 'like', "%{$search}%")
                             ->orWhere('no_hp', 'like', "%{$search}%");
                      });
                })
                ->latest()
                ->paginate(10);
        }

        /**
         * =========================
         * STEP 2 & 3 — LOAD TRANSACTION
         * =========================
         */
        if (in_array($step, [2, 3]) && $transactionId) {
            $transaction = Transaction::with([
                'patient',
                'items',
                'kasir'
            ])->findOrFail($transactionId);
        }

        return view('admin.transactions.pickup.index', compact(
            'step',
            'results',
            'transaction',
            'search'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Transaction $transaction, Request $request)
    {
        $request->validate([
            'nominal_bayar' => 'required|numeric|min:1',
            'metode_bayar'  => 'required|string',
        ]);

        $sudahBayar = $transaction->sudah_bayar ?? 0;
        $totalBayar = $transaction->total_bayar;

        $sudahBayar += $request->nominal_bayar;

        // Update pembayaran
        $transaction->update([
            'sudah_bayar' => $sudahBayar,
            'metode_bayar' => $request->metode_bayar,
            'status' => $sudahBayar >= $totalBayar ? 'lunas' : 'dp',
        ]);

        return redirect()->route('admin.transactions.pickup.index', [
            'step' => 2,
            'transaction_id' => $transaction->id
        ])->with('success', 'Pembayaran berhasil disimpan');
    }

    public function confirm(Request $request, Transaction $transaction)
    {
        // Validasi: harus lunas dulu
        if ($transaction->sisa > 0) {
            return back()->with('error', 'Transaksi belum lunas (masih ada sisa)');
        }

        // Update status pengambilan
        $transaction->update([
            'diambil' => 1,
        ]);

        return redirect()->back()->with('success', 'Pengambilan berhasil dikonfirmasi');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load(['patient', 'items.product']);

        return view('admin.transactions.pickup.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
