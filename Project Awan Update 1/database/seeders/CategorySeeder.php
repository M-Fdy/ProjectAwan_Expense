<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Kosan', 'type' => 'expense'],
            ['name' => 'Transportasi', 'type' => 'expense'],
            ['name' => 'Makan Harian', 'type' => 'expense'],
            ['name' => 'Tagihan Internet/Kuota', 'type' => 'expense'],
            ['name' => 'Hiburan & Rekreasi', 'type' => 'expense'],
            ['name' => 'Belanja Bulanan', 'type' => 'expense'],
            ['name' => 'Gaji', 'type' => 'income'],
            ['name' => 'Investasi', 'type' => 'income'],
            ['name' => 'Usaha', 'type' => 'income'],
            ['name' => 'Hadiah', 'type' => 'income'],
            ['name' => 'Lain-lain', 'type' => 'income'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate($category);
        }
    }
}
