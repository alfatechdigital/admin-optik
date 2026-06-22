<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Product, Category};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Exports\ProductExport;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode_produk', 'like', "%{$search}%")
                  ->orWhere('merek', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('stok') && $request->stok === 'menipis') {
            $query->whereColumn('stok', '<=', 'stok_minimum');
        }

        $products   = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::where('is_active', true)->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'nama'          => 'required|string|max:150',
            'deskripsi'     => 'nullable|string',
            'merek'         => 'nullable|string|max:100',
            'harga_beli'    => 'required|numeric|min:0',
            'harga_jual'    => 'required|numeric|min:0',
            'stok'          => 'required|integer|min:0',
            'stok_minimum'  => 'required|integer|min:0',
            'satuan'        => 'required|string|max:20',
            'gambar'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'     => 'boolean',
        ]);

        $validated['kode_produk'] = Product::generateKode();
        $validated['is_active']   = $request->boolean('is_active', true);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('products', 'public');
        }

        DB::transaction(function () use ($validated) {
            Product::create($validated);
        });

        return redirect()->route('products.index')
            ->with('success', "Produk {$validated['nama']} berhasil ditambahkan.");
    }

    public function show(Product $product)
    {
        $product->load('category');
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'nama'          => 'required|string|max:150',
            'deskripsi'     => 'nullable|string',
            'merek'         => 'nullable|string|max:100',
            'harga_beli'    => 'required|numeric|min:0',
            'harga_jual'    => 'required|numeric|min:0',
            'stok'          => 'required|integer|min:0',
            'stok_minimum'  => 'required|integer|min:0',
            'satuan'        => 'required|string|max:20',
            'gambar'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('gambar')) {
            if ($product->gambar) {
                Storage::disk('public')->delete($product->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->gambar) {
            Storage::disk('public')->delete($product->gambar);
        }
        $product->delete();
        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function export()
    {
        return Excel::download(new ProductExport, 'products_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        $file = $request->file('file');

        $headerError = $this->validateImportHeaders($file, ProductImport::expectedHeadings());
        if ($headerError) {
            return redirect()->route('products.index')
                ->with('error', $headerError);
        }

        $import = new ProductImport;

        try {
            DB::beginTransaction();

            Excel::import($import, $file);

            $errors = $import->getErrors();
            if (!empty($errors)) {
                DB::rollBack();

                $message = 'Import gagal: data tidak valid pada beberapa baris. ';
                $message .= implode(' | ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= ' (+' . (count($errors) - 5) . ' lainnya)';
                }

                return redirect()->route('products.index')
                    ->with('error', $message);
            }

            DB::commit();

            $successCount = $import->getSuccessCount();
            $message = "Import selesai. {$successCount} data berhasil diimpor.";

            return redirect()->route('products.index')
                ->with('success', $message);
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('products.index')
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    private function validateImportHeaders($file, array $expectedHeaders): ?string
    {
        $rows = (new HeadingRowImport)->toArray($file);
        $headers = $rows[0][0] ?? [];

        if (!is_array($headers) || empty($headers)) {
            return 'Format file tidak valid: tidak dapat membaca baris header.';
        }

        $normalize = fn ($value) => Str::of($value)
            ->trim()
            ->lower()
            ->replaceMatches('/\s+/', '_')
            ->replace('-', '_')
            ->__toString();

        $normalized = array_map($normalize, $headers);
        $expectedNormalized = array_map($normalize, $expectedHeaders);

        $missing = array_diff($expectedNormalized, $normalized);
        $extra = array_diff($normalized, $expectedNormalized);

        if (!empty($missing) || !empty($extra)) {
            $msgParts = [];
            if (!empty($missing)) {
                $msgParts[] = 'Kolom hilang: ' . implode(', ', $missing);
            }
            if (!empty($extra)) {
                $msgParts[] = 'Kolom tidak diharapkan: ' . implode(', ', $extra);
            }
            return 'Template file tidak sesuai. ' . implode(' ', $msgParts) . ' Pastikan menggunakan template import yang benar.';
        }

        return null;
    }
}
