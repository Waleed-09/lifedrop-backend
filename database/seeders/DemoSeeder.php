<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Abbottabad ka ek fake blood bank
        User::create([
            'name' => 'City Blood Bank',
            'email' => 'bank@lifedrop.test',
            'password' => Hash::make('password123'),
            'role' => 'bloodbank',
            'latitude' => 34.1463,
            'longitude' => 73.2114,
            'address' => 'Abbottabad, PK',
        ]);

        // Abbottabad ke 5 different available donors
        $groups = ['O+', 'O-', 'A+', 'B+', 'AB+'];
        foreach ($groups as $i => $group) {
            User::create([
                'name' => "Donor {$i}",
                'email' => "donor{$i}@lifedrop.test",
                'password' => Hash::make('password123'),
                'role' => 'donor',
                'blood_group' => $group,
                'availability' => true,
                'latitude' => 34.15 + ($i * 0.01),
                'longitude' => 73.21 + ($i * 0.01),
                'address' => 'Abbottabad, PK',
            ]);
        }
    }
}