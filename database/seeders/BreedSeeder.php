<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BreedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('breeds')->insert([
            [
                'id' => 1,
                'name' => 'Golden Retriever',
                'species_id' => 1, // Dog
                'clinic_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'name' => 'Persian',
                'species_id' => 2, // Cat
                'clinic_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
