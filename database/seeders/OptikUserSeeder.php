<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;

class OptikUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pastikan Role tersedia
        $roRole = Role::firstOrCreate(['name' => 'ro', 'guard_name' => 'web']);
        $salesRole = Role::firstOrCreate(['name' => 'sales_counter', 'guard_name' => 'web']);
        $foRole = Role::firstOrCreate(['name' => 'fo', 'guard_name' => 'web']);
        $dispensingRole = Role::firstOrCreate(['name' => 'dispensing', 'guard_name' => 'web']);
        $adminRole = Role::where('name', 'admin')->first();

        // 2. Sync Permissions (Sesuai tugas masing-masing)
        $roRole->syncPermissions([
            'patient.view', 
            'medical_record.view', 'medical_record.create', 'medical_record.edit',
            'product.view'
        ]);

        $salesRole->syncPermissions([
            'patient.view', 'patient.create', 'patient.edit',
            'transaction.view', 'transaction.create',
            'product.view'
        ]);

        $foRole->syncPermissions([
            'patient.view', 'patient.create', 'patient.edit',
            'transaction.view'
        ]);

        $dispensingRole->syncPermissions([
            'transaction.view', 
            'product.view'
        ]);

        // 3. Data User
        $users = [
            // RO (Refractionist Optician)
            ['name' => 'Ai Sahlah, A.Md.RO', 'roles' => ['ro']],
            ['name' => 'Komar, A.Md.RO', 'roles' => ['ro']],
            ['name' => 'Dani Lukmana, A.Md.Kes', 'roles' => ['ro']],
            ['name' => 'Fitri Pujiastuti, A.Md.RO', 'roles' => ['ro']],
            ['name' => 'Muhammad Firmansyah, A.Md.Kes', 'roles' => ['ro']],

            // Sales Counter / FO / Admin
            ['name' => 'Rudi Sukandi', 'roles' => ['sales_counter']],
            ['name' => 'Siti Audi M', 'roles' => ['admin', 'sales_counter']],
            ['name' => 'Evi Herlina', 'roles' => ['sales_counter']],
            ['name' => 'Anna Auliana', 'roles' => ['admin', 'fo']],
            ['name' => 'Winiralia', 'roles' => ['admin', 'sales_counter']],

            // Dispensing
            ['name' => 'Muhaji', 'roles' => ['dispensing']],
        ];

        foreach ($users as $userData) {
            // Generate email dari nama
            $cleanName = str_replace([',', '.'], '', $userData['name']);
            $email = Str::slug($cleanName, '') . '@optik.com';
            
            // Tambahkan 'admin' ke dalam roles jika belum ada
            $roles = $userData['roles'];
            if (!in_array('admin', $roles)) {
                $roles[] = 'admin';
            }

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                ]
            );

            $user->syncRoles($roles);
        }

        $this->command->info('✅ OptikUserSeeder: ' . count($users) . ' users seeded successfully.');
    }
}
