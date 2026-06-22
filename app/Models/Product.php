<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'kode_produk',
        'nama',
        'deskripsi',
        'merek',
        'harga_beli',
        'harga_jual',
        'stok',
        'stok_minimum',
        'satuan',
        'gambar',
        'is_active',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'harga_beli'  => 'decimal:2',
        'harga_jual'  => 'decimal:2',
    ];

    // Auto-generate kode produk
    public static function generateKode(): string
    {
        $last = static::withTrashed()->latest('id')->first();
        $num  = $last ? ((int) substr($last->kode_produk, 3)) + 1 : 1;
        return 'PRD' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    // Accessor stok menipis
    public function getStokMenipisAttribute(): bool
    {
        return $this->stok <= $this->stok_minimum;
    }

    // Accessor gambar URL
    public function getGambarUrlAttribute(): string
    {
        return $this->gambar
            ? asset('storage/' . $this->gambar)
            : asset('images/no-image.png');
    }

    // Relasi
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
