<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * The app hardcodes role_id comparisons (1=super admin, 2/3=clinic staff,
     * 4=veterinarian, 5=pet owner) throughout the controllers, so these roles
     * must keep these exact integer ids even though Mongo defaults to ObjectIds.
     */
    public function run()
    {
        $roles = [
            1 => 'Super Admin',
            2 => 'Clinic Admin',
            3 => 'Clinic Staff',
            4 => 'Veterinarian',
            5 => 'Pet Owner',
        ];

        foreach ($roles as $id => $name) {
            Role::where('_id', $id)->exists() || Role::create([
                '_id' => $id,
                'name' => $name,
            ]);
        }
    }
}
