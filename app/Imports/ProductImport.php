<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class ProductImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    private array $errors = [];
    private int $successCount = 0;

    public static function expectedHeadings(): array
    {
        return [
            'category_id',
            'nama',
            'deskripsi',
            'merek',
            'harga_beli',
            'harga_jual',
            'stok',
            'stok_minimum',
            'satuan',
            'is_active',
        ];
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $product = new Product([
            'category_id' => $row['category_id'],
            'kode_produk' => Product::generateKode(),
            'nama' => $row['nama'],
            'deskripsi' => $row['deskripsi'] ?? null,
            'merek' => $row['merek'] ?? null,
            'harga_beli' => $row['harga_beli'] ?? 0,
            'harga_jual' => $row['harga_jual'] ?? 0,
            'stok' => $row['stok'] ?? 0,
            'stok_minimum' => $row['stok_minimum'] ?? 0,
            'satuan' => $row['satuan'] ?? 'pcs',
            'is_active' => isset($row['is_active']) ? filter_var($row['is_active'], FILTER_VALIDATE_BOOLEAN) : true,
        ]);

        $product->save();
        $this->successCount++;

        return $product;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'nama' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'merek' => 'nullable|string|max:100',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * @param Throwable $e
     */
    public function onError(Throwable $e)
    {
        $this->errors[] = $e->getMessage();
    }

    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
        }
    }

    /**
     * Get the errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get the success count
     *
     * @return int
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }
}
