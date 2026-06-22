<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Patient;
use App\Models\Product;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // =====================
        // BUAT PERMISSIONS
        // =====================
        $permissions = [
            // User management
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            // Pasien
            'patient.view',
            'patient.create',
            'patient.edit',
            'patient.delete',
            // Rekam Medis
            'medical_record.view',
            'medical_record.create',
            'medical_record.edit',
            'medical_record.delete',
            // Produk
            'product.view',
            'product.create',
            'product.edit',
            'product.delete',
            // Kategori
            'category.view',
            'category.create',
            'category.edit',
            'category.delete',
            // Transaksi
            'transaction.view',
            'transaction.create',
            'transaction.edit',
            'transaction.delete',
            // Laporan
            'report.view',
            // Pengaturan
            'setting.view',
            'setting.edit',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // =====================
        // BUAT ROLES
        // =====================

        // Super Admin — akses semua
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // Admin — semua kecuali user management & setting
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions([
            'patient.view',
            'patient.create',
            'patient.edit',
            'patient.delete',
            'medical_record.view',
            'medical_record.create',
            'medical_record.edit',
            'medical_record.delete',
            'product.view',
            'product.create',
            'product.edit',
            'product.delete',
            'category.view',
            'category.create',
            'category.edit',
            'category.delete',
            'transaction.view',
            'transaction.create',
            'transaction.edit',
            'report.view',
        ]);

        // Kasir — hanya pasien, transaksi
        $kasir = Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);
        $kasir->syncPermissions([
            'patient.view',
            'patient.create',
            'patient.edit',
            'transaction.view',
            'transaction.create',
            'product.view',
        ]);

        // =====================
        // BUAT USERS
        // =====================
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@optik.com'],
            ['name' => 'Super Administrator', 'password' => Hash::make('password')]
        );
        $superAdminUser->assignRole('super_admin');

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@optik.com'],
            ['name' => 'Administrator', 'password' => Hash::make('password')]
        );
        $adminUser->assignRole('admin');

        $kasirUser = User::firstOrCreate(
            ['email' => 'kasir@optik.com'],
            ['name' => 'Kasir Utama', 'password' => Hash::make('password')]
        );
        $kasirUser->assignRole('kasir');

        // =====================
        // KATEGORI PRODUK
        // =====================
        $categories = [
            ['nama' => 'Frame Kacamata', 'deskripsi' => 'Bingkai kacamata berbagai merk & model'],
            ['nama' => 'Lensa Kacamata', 'deskripsi' => 'Lensa minus, plus, silinder, progresif, photochromic'],
            ['nama' => 'Kacamata Hitam', 'deskripsi' => 'Sunglasses fashion & sport'],
            ['nama' => 'Lensa Kontak', 'deskripsi' => 'Softlens harian, bulanan, tahunan'],
            ['nama' => 'Cairan & Aksesori', 'deskripsi' => 'Cairan pembersih, case, tali, lap'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['nama' => $cat['nama']], $cat);
        }

        // =====================
        // SAMPLE PRODUK
        // =====================
        $frameId = Category::where('nama', 'Frame Kacamata')->first()->id;
        $lensaId = Category::where('nama', 'Lensa Kacamata')->first()->id;
        $softlensId = Category::where('nama', 'Lensa Kontak')->first()->id;

        $products = [
            ['category_id' => $frameId, 'nama' => 'Frame Ray-Ban RB2140', 'merek' => 'Ray-Ban', 'harga_beli' => 350000, 'harga_jual' => 650000, 'stok' => 10],
            ['category_id' => $frameId, 'nama' => 'Frame Oakley OX8046', 'merek' => 'Oakley', 'harga_beli' => 400000, 'harga_jual' => 750000, 'stok' => 8],
            ['category_id' => $frameId, 'nama' => 'Frame Silhouette Slim', 'merek' => 'Silhouette', 'harga_beli' => 500000, 'harga_jual' => 950000, 'stok' => 5],
            ['category_id' => $lensaId, 'nama' => 'Lensa Essilor Single', 'merek' => 'Essilor', 'harga_beli' => 200000, 'harga_jual' => 450000, 'stok' => 20],
            ['category_id' => $lensaId, 'nama' => 'Lensa Hoya Progresif', 'merek' => 'Hoya', 'harga_beli' => 600000, 'harga_jual' => 1200000, 'stok' => 15],
            ['category_id' => $lensaId, 'nama' => 'Lensa Photochromic', 'merek' => 'Transitions', 'harga_beli' => 450000, 'harga_jual' => 900000, 'stok' => 12],
            ['category_id' => $softlensId, 'nama' => 'Acuvue Oasys Harian', 'merek' => 'Acuvue', 'harga_beli' => 80000, 'harga_jual' => 150000, 'stok' => 30],
            ['category_id' => $softlensId, 'nama' => 'Bausch Lomb Bulanan', 'merek' => 'B&L', 'harga_beli' => 120000, 'harga_jual' => 220000, 'stok' => 25],
        ];

        foreach ($products as $i => $prod) {
            $kode = 'PRD' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            Product::firstOrCreate(
                ['kode_produk' => $kode],
                array_merge($prod, ['kode_produk' => $kode])
            );
        }

        $this->command->info('✅ Seeder selesai! Akun: superadmin@optik.com / password');


        $this->call([
                // UserDoctorSeeder::class,
            OptikUserSeeder::class,
        ]);

        // Diagnosis seeder
        $this->call(DiagnosisSeeder::class);
    }
}
