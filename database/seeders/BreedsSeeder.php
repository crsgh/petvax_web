<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BreedsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $breeds = [
            // Dog breeds for clinic 1
            ['name' => 'Golden Retriever', 'species_id' => 1, 'clinic_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Labrador Retriever', 'species_id' => 1, 'clinic_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'German Shepherd', 'species_id' => 1, 'clinic_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bulldog', 'species_id' => 1, 'clinic_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Poodle', 'species_id' => 1, 'clinic_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Beagle', 'species_id' => 1, 'clinic_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rottweiler', 'species_id' => 1, 'clinic_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Yorkshire Terrier', 'species_id' => 1, 'clinic_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Cat breeds for clinic 1
            ['name' => 'Persian', 'species_id' => 2, 'clinic_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Siamese', 'species_id' => 2, 'clinic_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Maine Coon', 'species_id' => 2, 'clinic_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'British Shorthair', 'species_id' => 2, 'clinic_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ragdoll', 'species_id' => 2, 'clinic_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bengal', 'species_id' => 2, 'clinic_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Bird breeds for clinic 1
            ['name' => 'Budgerigar', 'species_id' => 3, 'clinic_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cockatiel', 'species_id' => 3, 'clinic_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lovebird', 'species_id' => 3, 'clinic_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Rabbit breeds for clinic 1
            ['name' => 'Holland Lop', 'species_id' => 4, 'clinic_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Netherland Dwarf', 'species_id' => 4, 'clinic_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            
            // Dog breeds for clinic 2
            ['name' => 'Shih Tzu', 'species_id' => 5, 'clinic_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Chihuahua', 'species_id' => 5, 'clinic_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dachshund', 'species_id' => 5, 'clinic_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            
            // Cat breeds for clinic 2
            ['name' => 'Russian Blue', 'species_id' => 6, 'clinic_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Scottish Fold', 'species_id' => 6, 'clinic_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('breeds')->insert($breeds);
    }
}
