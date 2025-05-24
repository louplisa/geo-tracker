<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Aurélie Ferré',
            'email' => 'ferre.aurelie@wanadoo.fr',
        ]);

        $postalCodes = ['75001', '13001', '31000', '59000', '44000'];

        foreach ($postalCodes as $code) {
            Location::factory()
                ->withPostalCode($code)
                ->create();
        }
    }
}
