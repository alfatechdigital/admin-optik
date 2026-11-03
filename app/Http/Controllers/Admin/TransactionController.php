<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Transaction, TransactionItem, Patient, Product, MedicalRecord};
use App\Services\MedicalRecordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['patient', 'kasir']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_transaksi', 'like', "%{$search}%")
                    ->orWhereHas('patient', fn($p) => $p->where('nama', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('diambil') && $request->diambil !== null) {
            if ($request->diambil == '1') {
                $query->where('diambil', 1);
            } else {
                $query->where(function ($q) {
                    $q->whereNull('diambil')->orWhere('diambil', '!=', 1);
                });
            }
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();
        return view('admin.transactions.index', compact('transactions'));
    }

    public function create(Transaction $transaction = null)
    {
        if ($transaction) {
            $transaction->load(['patient', 'items.product.category', 'medicalRecord.refraction', 'medicalRecord.prescription', 'medicalRecord.oldGlasses']);
        }

        $products = Product::where('is_active', true)
            ->where('stok', '>', 0)
            ->with('category')
            ->get();
        $patients = Patient::orderBy('nama')->get(['id', 'no_rm', 'nama', 'no_bpjs']);
        $doctors = \App\Models\User::orderBy('name')->get(['id', 'name']);
        $medRecs = collect();

        return view('admin.transactions.create', compact('products', 'patients', 'medRecs', 'transaction', 'doctors'));
    }

    public function store(Request $request)
    {
        // Normalize string to numeric input
        $request->merge([
            'bayar' => (int) str_replace('.', '', $request->bayar),
            'diskon_nominal' => (int) str_replace('.', '', $request->diskon_nominal),
            'potongan_bpjs' => (int) str_replace('.', '', $request->potongan_bpjs),
        ]);

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'metode_bayar' => 'required|in:tunai,transfer,qris,debit,kredit',
            'bayar' => 'required|numeric|min:0',
            'diskon_persen' => 'nullable|numeric|min:0|max:100',
            'diskon_nominal' => 'nullable|numeric|min:0',
            'potongan_bpjs' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $totalHarga = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stok < $item['qty']) {
                    throw new \Exception("Stok produk '{$product->nama}' tidak mencukupi. Stok saat ini: {$product->stok}");
                }

                $subtotal = $item['harga_satuan'] * $item['qty'];
                $totalHarga += $subtotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'nama_produk' => $product->nama,
                    'qty' => $item['qty'],
                    'harga_satuan' => $item['harga_satuan'],
                    'diskon' => $item['diskon'] ?? 0,
                    'subtotal' => $subtotal - ($item['diskon'] ?? 0),
                ];

                // Kurangi stok
                $product->decrement('stok', $item['qty']);
            }

            // Hitung diskon
            $diskonPersen = $request->diskon_persen ?? 0;
            $diskonNominal = $request->diskon_nominal ?? 0;
            if ($diskonPersen > 0) {
                $diskonNominal = round($totalHarga * ($diskonPersen / 100));
            }

            $potonganBpjs = $request->potongan_bpjs ?? 0;
            $totalBayar = $totalHarga - $diskonNominal - $potonganBpjs;
            $kembalian = $request->bayar - $totalBayar;

            // Double Protection
            if ($request->bayar < $totalBayar) {
                throw new \Exception('Jumlah bayar kurang dari total yang harus dibayar.');
            }

            $transaction = Transaction::create([
                'no_transaksi' => Transaction::generateNomor(),
                'patient_id' => $request->patient_id ?: null,
                'user_id' => auth()->id(),
                'medical_record_id' => $request->medical_record_id ?: null,
                'total_harga' => $totalHarga,
                'diskon_persen' => $diskonPersen,
                'diskon_nominal' => $diskonNominal,
                'potongan_bpjs' => $potonganBpjs,
                'total_bayar' => $totalBayar,
                'bayar' => $request->bayar,
                'kembalian' => max(0, $kembalian),
                'metode_bayar' => $request->metode_bayar,
                'status' => 'lunas',
                'catatan' => $request->catatan,
            ]);

            $transaction->items()->createMany($itemsData);
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['patient', 'kasir', 'medicalRecord.oldGlasses', 'medicalRecord.refraction', 'medicalRecord.prescription', 'items.product.category']);
        return view('admin.transactions.show', compact('transaction'));
    }

    public function printView(Transaction $transaction, Request $request)
    {
        $transaction->load(['patient', 'kasir', 'medicalRecord.prescription', 'medicalRecord.refraction', 'medicalRecord.oldGlasses', 'items.product.category']);

        $markup = $transaction->tambahan['markup'] ?? null;
        if ($markup) {
            $transaction->harga_jual = $markup['harga_jual'] ?? $transaction->harga_jual;
            $transaction->potongan = $markup['potongan'] ?? $transaction->potongan;
            $transaction->potongan_bpjs = $markup['potongan_bpjs'] ?? $transaction->potongan_bpjs;
            $transaction->dp = $markup['dp'] ?? $transaction->dp;
            $transaction->sisa = $markup['sisa'] ?? $transaction->sisa;
            $transaction->total_bayar = $markup['total_bayar'] ?? $transaction->total_bayar;
            $transaction->total_harga = $markup['harga_jual'] ?? $transaction->total_harga;
            $transaction->diskon_nominal = ($markup['potongan'] ?? 0) + ($markup['potongan_bpjs'] ?? 0);
            $transaction->bayar = $markup['dp'] ?? $transaction->bayar;

            if (!empty($markup['items'])) {
                $newItems = collect($markup['items'])->map(function ($i) {
                    $item = new \App\Models\TransactionItem();
                    $item->forceFill($i);
                    return $item;
                });
                $transaction->setRelation('items', $newItems);
            }
        }

        $type = $request->query('type', 'garansi');

        $views = [
            'garansi' => 'admin.transactions.print.garansi',
            'fasetan' => 'admin.transactions.print.fasetan',
            'pesanan_besar' => 'admin.transactions.print.pesanan_besar',
            'bon_3_rangkap' => 'admin.transactions.print.bon_3_rangkap',
            'bon_1_rangkap' => 'admin.transactions.print.bon_1_rangkap',
            'pelunasan' => 'admin.transactions.print.pelunasan',
            'label' => 'admin.transactions.print.label',
            'formulir_bpjs' => 'admin.transactions.print.formulir_bpjs',
        ];

        $viewName = $views[$type] ?? $views['garansi'];

        return view($viewName, compact('transaction'));
    }

    public function downloadBpjs(Transaction $transaction)
    {
        $transaction->load(['patient', 'kasir', 'items.product', 'medicalRecord']);

        $markup = $transaction->tambahan['markup'] ?? null;
        if ($markup) {
            $transaction->harga_jual = $markup['harga_jual'] ?? $transaction->harga_jual;
            $transaction->potongan = $markup['potongan'] ?? $transaction->potongan;
            $transaction->potongan_bpjs = $markup['potongan_bpjs'] ?? $transaction->potongan_bpjs;
            $transaction->dp = $markup['dp'] ?? $transaction->dp;
            $transaction->sisa = $markup['sisa'] ?? $transaction->sisa;
            $transaction->total_bayar = $markup['total_bayar'] ?? $transaction->total_bayar;
            $transaction->total_harga = $markup['harga_jual'] ?? $transaction->total_harga;
            $transaction->diskon_nominal = ($markup['potongan'] ?? 0) + ($markup['potongan_bpjs'] ?? 0);
            $transaction->bayar = $markup['dp'] ?? $transaction->bayar;

            if (!empty($markup['items'])) {
                $newItems = collect($markup['items'])->map(function ($i) {
                    $item = new \App\Models\TransactionItem();
                    $item->forceFill($i);
                    return $item;
                });
                $transaction->setRelation('items', $newItems);
            }
        }

        $template = new \PhpOffice\PhpWord\TemplateProcessor(resource_path('views/admin/transactions/print/formbpjs.docx'));

        $template->setValue('nama_pasien', $transaction->patient->nama ?? $transaction->nama_pasien ?? '-');
        $template->setValue('no_bpjs', $transaction->patient->no_bpjs ?? $transaction->no_bpjs ?? '-');
        $template->setValue('alamat', $transaction->patient->alamat ?? $transaction->alamat_pasien ?? '-');
        $template->setValue(
            'tanggal',
            \Carbon\Carbon::parse($transaction->tgl_order ?? $transaction->created_at)
                ->locale('id')
                ->translatedFormat('d F Y')
        );
        $template->setValue('1', $transaction->od_sph ?: '-'); // OD SPH
        $template->setValue('2', $transaction->od_cyl ?: '-'); // OD CYL
        $template->setValue('3', $transaction->od_axis ?: '-'); // OD AXIS
        $template->setValue('4', $transaction->od_add ?: '-'); // OD ADD
        $template->setValue('5', $transaction->os_sph ?: '-'); // OS SPH
        $template->setValue('6', $transaction->os_cyl ?: '-'); // OS CYL
        $template->setValue('7', $transaction->os_axis ?: '-'); // OS AXIS
        $template->setValue('8', $transaction->os_add ?: '-'); // OS ADD

        $template->setValue('add_plus', $transaction->od_add ?: ($transaction->os_add ?: '-'));
        $template->setValue('dv', $transaction->od_mpd ?: ($transaction->os_mpd ?: '-'));

        $frameName = '-';
        $lensaName = '-';
        $framePrice = 0;
        $lensaPrice = 0;

        foreach ($transaction->items as $item) {
            $nameLower = strtolower($item->nama_produk);
            if (str_contains($nameLower, 'frame')) {
                $frameName = $item->nama_produk;
                $framePrice += $item->subtotal;
            } elseif (str_contains($nameLower, 'lensa')) {
                $lensaName = $item->nama_produk;
                $lensaPrice += $item->subtotal;
            } else {
                if ($frameName === '-') {
                    $frameName = $item->nama_produk;
                    $framePrice += $item->subtotal;
                } else {
                    $lensaName = $item->nama_produk;
                    $lensaPrice += $item->subtotal;
                }
            }
        }

        if ($frameName === '-' && $transaction->kode_frame) {
            $frameName = trim($transaction->kode_frame . ' ' . $transaction->nama_produk);
        }
        if ($lensaName === '-' && $transaction->lensa) {
            $lensaName = $transaction->lensa;
        }
        if ($framePrice == 0 && $lensaPrice == 0) {
            $framePrice = $transaction->harga_jual;
        }

        $template->setValue('lensa', $lensaName);
        $template->setValue('frame', $frameName);
        // 1. Hitung total secara dinamis dari variabel harga komponen
        $totalBiaya = $lensaPrice + $framePrice;

        // 2. Terapkan nilai ke template dengan format Rupiah
        $template->setValue('harga_lensa', number_format($lensaPrice, 0, ',', '.'));
        $template->setValue('harga_frame', number_format($framePrice, 0, ',', '.'));
        $template->setValue('total_biaya', number_format($totalBiaya, 6, ',', '.'));
        $template->setValue('bpjs_cover', number_format($transaction->potonganBpjs, 0, ',', '.'));
        $template->setValue('selisih_bayar', number_format($transaction->total_bayar, 0, ',', '.'));

        $filename = 'Form_BPJS_' . str_replace('/', '_', $transaction->no_transaksi) . '_' . str_replace(' ', '_', $transaction->patient->nama ?? $transaction->nama_pasien ?? 'Pasien') . '.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'bpjs');
        $template->saveAs($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    public function downloadNotaBesar(Transaction $transaction, Request $request)
    {
        $transaction->load(['patient', 'kasir', 'items.product', 'medicalRecord']);

        $markup = $transaction->tambahan['markup'] ?? null;
        if ($markup) {
            $transaction->harga_jual = $markup['harga_jual'] ?? $transaction->harga_jual;
            $transaction->potongan = $markup['potongan'] ?? $transaction->potongan;
            $transaction->potongan_bpjs = $markup['potongan_bpjs'] ?? $transaction->potongan_bpjs;
            $transaction->dp = $markup['dp'] ?? $transaction->dp;
            $transaction->sisa = $markup['sisa'] ?? $transaction->sisa;
            $transaction->total_bayar = $markup['total_bayar'] ?? $transaction->total_bayar;
            $transaction->total_harga = $markup['harga_jual'] ?? $transaction->total_harga;
            $transaction->diskon_nominal = ($markup['potongan'] ?? 0) + ($markup['potongan_bpjs'] ?? 0);
            $transaction->bayar = $markup['dp'] ?? $transaction->bayar;

            if (!empty($markup['items'])) {
                $newItems = collect($markup['items'])->map(function ($i) {
                    $item = new \App\Models\TransactionItem();
                    $item->forceFill($i);
                    return $item;
                });
                $transaction->setRelation('items', $newItems);
            }
        }

        $template = new \PhpOffice\PhpWord\TemplateProcessor(resource_path('views/admin/transactions/print/nota besar.docx'));

        $showDetail = $request->query('show_detail', '1') === '1';

        $harga = $transaction->harga_jual ?? $transaction->total_harga ?? 0;
        $potongan = $transaction->potongan ?? $transaction->diskon_nominal ?? 0;
        $potonganBpjs = $transaction->potongan_bpjs ?? 0;
        $tipeBpjs = $transaction->patient->tipe_bpjs ?? $transaction->tipe_bpjs ?? '1';
        $totalHarusDibayar = $transaction->total_bayar ?? ($harga - $potongan - $potonganBpjs);
        $namaKasir = $transaction->kasir->name ?? 'Kasir';

        $tglFormatted = \Carbon\Carbon::parse($transaction->tgl_order ?? $transaction->created_at)
            ->locale('id')
            ->translatedFormat('j F Y');

        // Main placeholders
        $template->setValue('no', $transaction->no_transaksi);
        $template->setValue('no_transaksi', $transaction->no_transaksi);

        $namaPasien = $transaction->patient->nama ?? ($transaction->nama_pasien ?? 'UMUM');
        $template->setValue('from', $namaPasien);
        $template->setValue('nama_pasien', $namaPasien);

        $template->setValue('date', $tglFormatted);

        $template->setValue('all', number_format($harga, 0, ',', '.'));
        $template->setValue('harga', 'Rp. ' . number_format($harga, 0, ',', '.'));

        $template->setValue('pay', number_format($totalHarusDibayar, 0, ',', '.'));
        $template->setValue('total', 'Rp. ' . number_format($totalHarusDibayar, 0, ',', '.'));

        $terbilangText = $this->terbilangNotaBesar($totalHarusDibayar);
        $template->setValue('word', $terbilangText);
        $template->setValue('terbilang', $terbilangText);
        $template->setValue('kasir', $namaKasir);

        // --- 1. DISKON / POTONGAN ---
        if ($potongan > 0) {
            $template->setValue('sale', number_format($potongan, 0, ',', '.'));
            $template->setValue('potongan', 'Rp. ' . number_format($potongan, 0, ',', '.'));
            try {
                $template->cloneRow('row_diskon', 1);
            } catch (\Exception $e) {
            }
        } else {
            $template->setValue('sale', '');
            $template->setValue('potongan', '');
            $template->setValue('Diskon', '');
            try {
                // Menghapus total 1 baris tabel diskon
                $template->cloneRow('row_diskon', 0);
            } catch (\Exception $e) {
            }
        }

        // --- 2. POTONGAN BPJS ---
        $isBpjs = ($transaction->typefaktur == 2 || $potonganBpjs > 0 || !empty($transaction->no_bpjs) || !empty($transaction->patient?->no_bpjs));
        if ($isBpjs && $potonganBpjs > 0) {
            $template->setValue('bpjs', number_format($potonganBpjs, 0, ',', '.'));
            $template->setValue('tipe', $tipeBpjs);
            $template->setValue('tipe_bpjs', $tipeBpjs);
            try {
                $template->cloneRow('row_bpjs', 1);
            } catch (\Exception $e) {
            }
        } else {
            $template->setValue('bpjs', '');
            $template->setValue('tipe', '');
            $template->setValue('tipe_bpjs', '');
            $template->setValue('Potongan BPJS Kelas ${tipe}', '');
            try {
                // Menghapus total 1 baris tabel BPJS
                $template->cloneRow('row_bpjs', 0);
            } catch (\Exception $e) {
            }
        }

        // Processing items (${for} / ${nama_produk}):
        $items = $transaction->items;
        if ($items->count() > 0) {
            $rowVar = 'for';
            try {
                try {
                    $template->cloneRow('for', $items->count());
                    $rowVar = 'for';
                } catch (\Exception $eRow) {
                    $template->cloneRow('nama_produk', $items->count());
                    $rowVar = 'nama_produk';
                }

                foreach ($items as $index => $item) {
                    $i = $index + 1;
                    $penyebutQty = strtolower(trim($this->penyebutNotaBesar($item->qty)));
                    $forText = $item->qty . '(' . $penyebutQty . ') ' . $item->nama_produk;

                    $template->setValue($rowVar . '#' . $i, $forText);
                    $template->setValue('qty#' . $i, $item->qty . '(' . $penyebutQty . ')');
                    $template->setValue('nama_produk#' . $i, $item->nama_produk);

                    $priceVal = $showDetail ? 'Rp. ' . number_format($item->harga_satuan * $item->qty, 0, ',', '.') : '';
                    $template->setValue('price#' . $i, $priceVal);
                }
            } catch (\Exception $e) {
                $forLines = [];
                $priceLines = [];

                foreach ($items as $item) {
                    $penyebutQty = strtolower(trim($this->penyebutNotaBesar($item->qty)));
                    $forLines[] = $item->qty . '(' . $penyebutQty . ') ' . $item->nama_produk;
                    $priceLines[] = 'Rp. ' . number_format($item->harga_satuan * $item->qty, 0, ',', '.');
                }

                $joinedFor = implode("<w:br/>", $forLines);
                $template->setValue('for', $joinedFor);
                $template->setValue('nama_produk', $joinedFor);
                $template->setValue('qty', '');
                $template->setValue('price', $showDetail ? implode("<w:br/>", $priceLines) : '');
            }
        } else {
            $template->setValue('for', '-');
            $template->setValue('nama_produk', '-');
            $template->setValue('qty', '');
            $template->setValue('price', '');
        }

        $filename = 'Nota_Besar_' . str_replace('/', '_', $transaction->no_transaksi) . '.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'nota_besar');
        $template->saveAs($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
    private function penyebutNotaBesar($nilai)
    {
        $nilai = abs($nilai);
        $huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $this->penyebutNotaBesar($nilai - 10) . " Belas";
        } else if ($nilai < 100) {
            $temp = $this->penyebutNotaBesar($nilai / 10) . " Puluh" . $this->penyebutNotaBesar($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " Seratus" . $this->penyebutNotaBesar($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->penyebutNotaBesar($nilai / 100) . " Ratus" . $this->penyebutNotaBesar($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " Seribu" . $this->penyebutNotaBesar($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->penyebutNotaBesar($nilai / 1000) . " Ribu" . $this->penyebutNotaBesar($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->penyebutNotaBesar($nilai / 1000000) . " Juta" . $this->penyebutNotaBesar($nilai % 1000000);
        }
        return $temp;
    }

    private function terbilangNotaBesar($nilai)
    {
        if ($nilai == 0)
            return "Nol Rupiah";
        $hasil = trim($this->penyebutNotaBesar($nilai));
        return $hasil . " Rupiah";
    }

    public function destroy(Transaction $transaction)
    {
        DB::transaction(function () use ($transaction) {
            foreach ($transaction->items as $item) {
                if ($item->product) {
                    $item->product->increment('stok', $item->qty);
                }
            }
            $transaction->update(['status' => 'batal']);
            $transaction->delete();
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus dan stok telah dikembalikan.');
    }

    public function cancel(Transaction $transaction)
    {
        if ($transaction->status === 'batal') {
            return back()->with('error', 'Transaksi ini sudah dibatalkan sebelumnya.');
        }

        DB::transaction(function () use ($transaction) {
            foreach ($transaction->items as $item) {
                if ($item->product) {
                    $item->product->increment('stok', $item->qty);
                }
            }
            $transaction->update(['status' => 'batal']);
        });

        return back()->with('success', 'Transaksi berhasil dibatalkan dan stok telah dikembalikan.');
    }

    public function searchProduct(Request $request)
    {
        $products = Product::where('is_active', true)
            ->where('stok', '>', 0)
            ->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->q}%")
                    ->orWhere('kode_produk', 'like', "%{$request->q}%")
                    ->orWhere('merek', 'like', "%{$request->q}%");
            })
            ->with('category')
            ->limit(10)
            ->get(['id', 'kode_produk', 'nama', 'merek', 'harga_jual', 'stok', 'category_id']);

        return response()->json($products);
    }

    public function getMedicalRecords(Request $request)
    {
        if (!$request->patient_id) {
            return response()->json([]);
        }

        $records = MedicalRecord::where('patient_id', $request->patient_id)
            ->with('refraction')
            ->latest('visit_date')
            ->take(10)
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'tanggal_kunjungan' => $r->visit_date->format('d M Y'),
                    'od_sph' => $r->refraction?->od_sph ?? '-',
                    'os_sph' => $r->refraction?->os_sph ?? '-',
                ];
            });

        return response()->json($records);
    }

    public function posNav(Request $request)
    {
        $dir = $request->dir;
        $currentId = $request->current_id;

        $query = Transaction::with(['patient', 'items.product', 'medicalRecord']);

        if ($dir == 'awal') {
            $trx = $query->orderBy('id', 'asc')->first();
        } elseif ($dir == 'akhir') {
            $trx = $query->orderBy('id', 'desc')->first();
        } elseif ($dir == 'sebelum' && $currentId) {
            $trx = $query->where('id', '<', $currentId)->orderBy('id', 'desc')->first();
        } elseif ($dir == 'sesudah' && $currentId) {
            $trx = $query->where('id', '>', $currentId)->orderBy('id', 'asc')->first();
        } else {
            $trx = null;
        }

        return response()->json($trx ?: null);
    }

    public function posSearch(Request $request)
    {
        $q = $request->q;
        $query = Transaction::with('patient')->latest();

        if ($q) {
            $query->where('no_transaksi', 'like', "%{$q}%")
                ->orWhereHas('patient', function ($p) use ($q) {
                    $p->where('nama', 'like', "%{$q}%")
                        ->orWhere('no_bpjs', 'like', "%{$q}%");
                });
        }

        $transactions = $query->paginate(10)->through(function ($t) {
            return [
                'id' => $t->id,
                'no_transaksi' => $t->no_transaksi,
                'tanggal' => $t->created_at->format('d/m/Y'),
                'pasien' => $t->patient ? $t->patient->nama : ($t->nama_pasien ?? '-'),
                'total' => 'Rp ' . number_format($t->total_bayar, 0, ',', '.'),
                'is_bpjs' => $t->potongan_bpjs > 0,
            ];
        });

        return response()->json($transactions);
    }

    public function posSave(Request $request)
    {
        $request->merge([
            'harga_jual' => (int) str_replace('.', '', $request->harga_jual),
            'dp' => (int) str_replace('.', '', $request->dp),
            'pelunasan' => (int) str_replace('.', '', $request->pelunasan),
            'potongan' => (int) str_replace('.', '', $request->potongan),
            'potongan_bpjs' => (int) str_replace('.', '', $request->potongan_bpjs),
        ]);

        try {
            DB::beginTransaction();

            $patient = null;
            if ($request->patient_id) {
                $patient = Patient::find($request->patient_id);
                if ($patient && $request->nama) {
                    $patient->update([
                        'nama' => $request->nama,
                        'nik' => $request->nik,
                        'no_bpjs' => $request->no_bpjs,
                        'no_hp' => $request->telp,
                        'alamat' => $request->alamat,
                    ]);
                } elseif (!$request->nama) {
                    $patient = null;
                }
            } elseif ($request->nama) {
                $patient = Patient::where('nama', $request->nama)->where('no_hp', $request->telp)->first();
                if (!$patient) {
                    $patient = Patient::create([
                        'no_rm' => Patient::generateNoRM(),
                        'nama' => $request->nama,
                        'nik' => $request->nik,
                        'no_bpjs' => $request->no_bpjs,
                        'no_hp' => $request->telp,
                        'alamat' => $request->alamat,
                    ]);
                }
            }

            $cartData = json_decode($request->cart_data, true) ?: [];

            $firstFrame = collect($cartData)->firstWhere('type', 'Frame');
            $firstLensa = collect($cartData)->firstWhere('type', 'Lensa');
            if (!$firstFrame && count($cartData) > 0)
                $firstFrame = $cartData[0];

            $firstFrame = $firstFrame ?: [];
            $firstLensa = $firstLensa ?: [];

            $kodeFrameReq = $request->kode_frame;
            $namaProdukReq = $request->nama_produk;
            $seriReq = $request->seri;
            $warnaReq = $request->warna;
            $keteranganReq = $request->keterangan;

            $kodeFrame = $firstFrame['kode_produk'] ?? ($firstFrame['product_id'] ?? (is_array($kodeFrameReq) ? ($kodeFrameReq[0] ?? null) : $kodeFrameReq));
            $namaProduk = $firstFrame['nama'] ?? ($firstFrame['nama_produk'] ?? (is_array($namaProdukReq) ? ($namaProdukReq[0] ?? null) : $namaProdukReq));
            $seri = $firstFrame['seri'] ?? (is_array($seriReq) ? ($seriReq[0] ?? null) : $seriReq);
            $warna = $firstFrame['warna'] ?? (is_array($warnaReq) ? ($warnaReq[0] ?? null) : $warnaReq);
            $keterangan = $firstFrame['keterangan'] ?? (is_array($keteranganReq) ? ($keteranganReq[0] ?? null) : $keteranganReq);

            $lensaCode = $firstLensa['kode_produk'] ?? '';
            $lensaName = $firstLensa['nama'] ?? ($firstLensa['nama_produk'] ?? ($request->lensa ?? '-'));
            $lensa = ($lensaCode ? '[' . $lensaCode . '] ' : '') . $lensaName;

            $data = [
                'patient_id' => $patient ? $patient->id : null,
                'user_id' => auth()->id(),
                'status' => 'lunas',
                'metode_bayar' => 'tunai',

                'tgl_order' => $request->tgl_order ?: null,
                'tgl_faset' => $request->tgl_faset,
                'lab' => $request->lab,
                'tempat_faset' => $request->tempat_faset,
                'tgl_datang_faset' => $request->tgl_datang_faset,
                'tgl_selesai_faset' => $request->tgl_selesai_faset,
                'tgl_selesai_janji' => $request->tgl_selesai_janji,
                'tgl_diambil' => $request->tgl_diambil,
                'no_legalisasi' => $request->no_legalisasi,
                'tgl_legalisasi' => $request->tgl_legalisasi,
                'tgl_refraksi' => $request->tgl_refraksi,
                'catatan' => $request->catatan,

                'od_sc' => $request->od_sc,
                'os_sc' => $request->os_sc,
                'od_cc' => $request->presc_od_cc ?? $request->od_cc,
                'os_cc' => $request->presc_os_cc ?? $request->os_cc,
                'od_sph' => $request->presc_od_sph ?? $request->od_sph,
                'od_cyl' => $request->presc_od_cyl ?? $request->od_cyl,
                'od_axis' => $request->presc_od_axis ?? $request->od_axis,
                'od_add' => $request->presc_od_add ?? $request->od_add,
                'od_mpd' => $request->presc_od_mpd ?? $request->od_mpd,
                'od_prism' => $request->presc_od_prism ?? $request->od_prism,
                'os_sph' => $request->presc_os_sph ?? $request->os_sph,
                'os_cyl' => $request->presc_os_cyl ?? $request->os_cyl,
                'os_axis' => $request->presc_os_axis ?? $request->os_axis,
                'os_add' => $request->presc_os_add ?? $request->os_add,
                'os_mpd' => $request->presc_os_mpd ?? $request->os_mpd,
                'os_prism' => $request->presc_os_prism ?? $request->os_prism,

                'nik' => $request->nik,
                'no_bpjs' => $request->no_bpjs,
                'nama_pasien' => $request->nama,
                'alamat_pasien' => $request->alamat,
                'telp_pasien' => $request->telp,
                'asal_resep' => $request->asal_resep,
                'nama_dokter' => $request->nama_dokter,

                'lensa' => $lensa,
                'kode_frame' => $kodeFrame,
                'nama_produk' => $namaProduk,
                'keterangan_frame' => $keterangan,
                'seri' => $seri,
                'warna' => $warna,
                'typefaktur' => $request->typefaktur,
                'diambil' => $request->diambil,

                'harga_jual' => $request->harga_jual,
                'dp' => $request->dp,
                'pelunasan' => $request->pelunasan,
                'potongan' => $request->potongan,
                'potongan_bpjs' => $request->potongan_bpjs,
                'sisa' => max(0, $request->harga_jual - $request->dp - ($request->pelunasan ?? 0) - ($request->potongan ?? 0) - ($request->potongan_bpjs ?? 0)),

                'total_harga' => $request->harga_jual ?? 0,
                'total_bayar' => max(0, ($request->harga_jual ?? 0) - ($request->potongan ?? 0) - ($request->potongan_bpjs ?? 0)),
                'bayar' => ($request->dp ?? 0) + ($request->pelunasan ?? 0),
                'diskon_nominal' => ($request->potongan ?? 0) + ($request->potongan_bpjs ?? 0),
            ];

            if ($request->id) {
                $trx = Transaction::findOrFail($request->id);
                $oldMedicalRecordId = $trx->medical_record_id; // Capture old medical record ID
                $trx->update($data);
                $msg = 'Transaksi berhasil diupdate';

                foreach ($trx->items as $oldItem) {
                    if ($oldItem->product) {
                        $oldItem->product->increment('stok', $oldItem->qty);
                    }
                }
            } else {
                $maxRetries = 5;
                $retryCount = 0;
                $trx = null;

                while ($retryCount < $maxRetries) {
                    try {
                        $data['no_transaksi'] = Transaction::generateNomor();
                        $trx = Transaction::create($data);
                        break;
                    } catch (\Illuminate\Database\QueryException $e) {
                        if ($e->errorInfo[1] == 1062) {
                            $retryCount++;
                            if ($retryCount >= $maxRetries)
                                throw $e;
                            usleep(100000);
                        } else {
                            throw $e;
                        }
                    }
                }
                $msg = 'Transaksi berhasil disimpan';
            }

            $trx->items()->delete();

            $cartData = json_decode($request->cart_data, true) ?: [];
            if (is_array($cartData) && count($cartData) > 0) {
                foreach ($cartData as $item) {
                    if (empty($item['nama']))
                        continue;

                    $product = null;
                    if (!empty($item['product_id'])) {
                        $product = Product::find($item['product_id']);
                    }

                    $qty = max(1, intval($item['qty'] ?? 1));

                    if ($product) {
                        if ($product->stok < $qty) {
                            throw new \Exception("Stok produk '{$product->nama}' tidak mencukupi (Tersisa: {$product->stok})");
                        }
                        $product->decrement('stok', $qty);
                    }

                    $trx->items()->create([
                        'product_id' => $product ? $product->id : null,
                        'nama_produk' => $item['nama'] ?? ($product->nama ?? 'Unknown'),
                        'type' => $item['type'] ?? null,
                        'qty' => $qty,
                        'harga_satuan' => floatval($item['harga'] ?? 0),
                        'subtotal' => floatval($item['harga'] ?? 0) * $qty
                    ]);
                }
            } else {
                $kodeFrames = is_array($request->kode_frame) ? $request->kode_frame : [$request->kode_frame];
                foreach ($kodeFrames as $idx => $kode) {
                    if (!$kode)
                        continue;
                    $product = Product::where('kode_produk', $kode)->lockForUpdate()->first();
                    if ($product) {
                        if ($product->stok < 1) {
                            throw new \Exception("Stok frame '{$product->nama}' habis.");
                        }
                        $product->decrement('stok', 1);
                    }

                    $trx->items()->create([
                        'product_id' => $product ? $product->id : null,
                        'nama_produk' => $product ? $product->nama : 'Frame/Produk',
                        'qty' => 1,
                        'harga_satuan' => $request->harga_jual,
                        'subtotal' => $request->harga_jual
                    ]);
                }
            }

            DB::commit();

            if ($patient) {
                try {
                    $medicalRecordService = app(\App\Services\MedicalRecordService::class);
                    $mr = $medicalRecordService->saveFromTransaction($patient->id, $request->all());
                    $trx->update(['medical_record_id' => $mr->id]);
                } catch (\Exception $e) {
                    \Log::error("Failed to save medical record from POS: " . $e->getMessage());
                }
            }

            return response()->json(['status' => 'success', 'message' => $msg, 'data' => $trx]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function posDelete($id)
    {
        try {
            $trx = Transaction::findOrFail($id);
            if ($trx->items()->count() > 0) {
                $trx->items()->delete();
            }
            $trx->delete();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function patientAutocomplete(Request $request)
    {
        $q = $request->q;
        $patients = Patient::where('no_bpjs', 'like', "%{$q}%")
            ->orWhere('no_rm', 'like', "%{$q}%")
            ->orWhere('nik', 'like', "%{$q}%")
            ->orWhere('nama', 'like', "%{$q}%")
            ->orWhere('no_hp', 'like', "%{$q}%")
            ->take(10)->get();
        return response()->json($patients);
    }

    public function frameAutocomplete(Request $request)
    {
        $q = $request->q;
        $type = $request->query('type');

        $query = Product::where(function ($builder) use ($q) {
            $builder->where('kode_produk', 'like', "%{$q}%")
                ->orWhere('nama', 'like', "%{$q}%")
                ->orWhere('merek', 'like', "%{$q}%");
        });

        if ($type === 'Frame') {
            $query->whereHas('category', function ($cat) {
                $cat->where('nama', 'like', '%Frame%');
            });
        } elseif ($type === 'Lensa') {
            $query->whereHas('category', function ($cat) {
                $cat->where('nama', 'like', '%Lensa%');
            });
        }

        $products = $query->take(10)->get();
        return response()->json($products);
    }

    public function saveMarkup(Request $request, Transaction $transaction)
    {
        // Normalize money inputs
        $request->merge([
            'harga_jual' => (int) str_replace('.', '', $request->harga_jual),
            'dp' => (int) str_replace('.', '', $request->dp),
            'potongan' => (int) str_replace('.', '', $request->potongan),
            'potongan_bpjs' => (int) str_replace('.', '', $request->potongan_bpjs),
            'sisa' => (int) str_replace('.', '', $request->sisa),
            'total_bayar' => (int) str_replace('.', '', $request->total_bayar),
        ]);

        $sisa = $request->sisa ?? 0;

        // Proteksi Logika ENUM: Jika cicilan (sisa > 0), kembalikan ke status lama bawaan transaksi 
        // (misalnya status awalnya adalah 'indent' atau status lama yang sah di database Anda).
        // Jangan pernah kirim string 'belum lunas' jika tidak terdaftar di ENUM database.
        $validStatus = $sisa <= 0 ? 'lunas' : $transaction->status;

        $markupData = [
            'items' => $request->items ?? [],
            'harga_jual' => $request->harga_jual ?? 0,
            'dp' => $request->dp ?? 0,
            'potongan' => $request->potongan ?? 0,
            'potongan_bpjs' => $request->potongan_bpjs ?? 0,
            'sisa' => $sisa,
            'total_bayar' => $request->total_bayar ?? 0,
            'status' => $validStatus,
        ];

        if ($request->save_mode === 'permanent') {
            try {
                DB::transaction(function () use ($transaction, $markupData, $sisa, $validStatus) {
                    // Update item transaksi utama
                    $transaction->items()->delete();
                    foreach ($markupData['items'] as $item) {
                        $transaction->items()->create([
                            'product_id' => $item['product_id'] ?? null,
                            'nama_produk' => $item['nama_produk'] ?? '-',
                            'qty' => max(1, (int) $item['qty']),
                            'harga_satuan' => (float) $item['harga_satuan'],
                            'subtotal' => (float) $item['subtotal']
                        ]);
                    }

                    $dp = $markupData['dp'];
                    $harga_jual = $markupData['harga_jual'];
                    $potongan = $markupData['potongan'];
                    $potongan_bpjs = $markupData['potongan_bpjs'];

                    // Update kolom transaksi utama dengan status yang valid di ENUM
                    $transaction->update([
                        'harga_jual' => $harga_jual,
                        'dp' => $dp,
                        'potongan' => $potongan,
                        'potongan_bpjs' => $potongan_bpjs,
                        'sisa' => $sisa,
                        'total_harga' => $harga_jual,
                        'total_bayar' => max(0, $harga_jual - $potongan - $potongan_bpjs),
                        'bayar' => $dp,
                        'diskon_nominal' => $potongan + $potongan_bpjs,
                        'status' => $validStatus
                    ]);

                    // Hapus data markup sementara jika ada, karena sudah dipermanenkan
                    $tambahan = $transaction->tambahan ?? [];
                    if (isset($tambahan['markup'])) {
                        unset($tambahan['markup']);
                        $transaction->update(['tambahan' => $tambahan]);
                    }
                });

                return response()->json([
                    'status' => 'success',
                    'message' => 'Data permanen berhasil disimpan dan telah menimpa database utama.'
                ]);

            } catch (\Illuminate\Database\QueryException $e) {
                // Tangkap jika query mysql masih bermasalah dengan ENUM
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal memperbarui status ke database. Detail: ' . $e->getMessage()
                ], 500);
            }
        } else {
            // Mode Sementara (Temporary)
            $tambahan = $transaction->tambahan ?? [];
            $tambahan['markup'] = $markupData;

            $transaction->update(['tambahan' => $tambahan]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data markup sementara berhasil disimpan.',
                'data' => $markupData
            ]);
        }
    }
}