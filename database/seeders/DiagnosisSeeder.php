<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiagnosisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $diagnoses = [
            ['code' => 'D001', 'name' => 'Myopia Astigmatism', 'description' => 'Kondisi di mana mata memiliki kelainan refraksi yang menyebabkan penglihatan kabur pada semua jarak.'],
            ['code' => 'D002', 'name' => 'Myopia + Presbyopia', 'description' => 'Kondisi di mana mata memiliki kelainan refraksi yang menyebabkan penglihatan kabur pada semua jarak.'],
            ['code' => 'D003', 'name' => 'Myopia Astigmatism + Presbyopia', 'description' => 'Kondisi di mana mata memiliki kelainan refraksi yang menyebabkan penglihatan kabur pada semua jarak.'],
            ['code' => 'D004', 'name' => 'Hypermetropia + Astigmatism', 'description' => 'Kondisi di mana mata memiliki kelainan refraksi yang menyebabkan penglihatan kabur pada semua jarak.'],
            ['code' => 'D005', 'name' => 'Hypermetropia + Presbyopia', 'description' => 'Kondisi di mana mata memiliki kelainan refraksi yang menyebabkan penglihatan kabur pada semua jarak.'],
            ['code' => 'D006', 'name' => 'Hypermetropia Astigmatism + Presbyopia', 'description' => 'Kondisi di mana mata memiliki kelainan refraksi yang menyebabkan penglihatan kabur pada semua jarak.'],
            ['code' => 'D007', 'name' => 'Astigmatism + Presbyopia', 'description' => 'Kondisi di mana mata memiliki kelainan refraksi yang menyebabkan penglihatan kabur pada semua jarak.'],
        ];

        foreach ($diagnoses as $diagnosis) {
            \App\Models\Diagnosis::create($diagnosis);
        }
    }
}
