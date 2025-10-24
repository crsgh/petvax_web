<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'id' => 1,
                'name' => 'Medicine',
                'description' => 'Medical supplies and medications',
                'status' => 'active',
                'clinic_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'name' => 'Equipment',
                'description' => 'Medical equipment and tools',
                'status' => 'active',
                'clinic_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'name' => 'Supplies',
                'description' => 'General medical supplies',
                'status' => 'active',
                'clinic_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 4,
                'name' => 'Food & Treats',
                'description' => 'Pet food and treats',
                'status' => 'active',
                'clinic_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
