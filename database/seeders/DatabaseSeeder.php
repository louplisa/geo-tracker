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
         User::factory(10)->create();

        User::factory()->create([
            'name' => 'Aurélie Ferré',
            'email' => 'ferre.aurelie@wanadoo.fr',
        ]);

        $cities = [
            'Paris' => '75001',
            'Marseille' => '13001',
            'Toulouse' => '31000',
            'Lille' => '59000',
            'Lyon' => '69001'
        ];

        foreach ($cities as $name => $zipCode) {
            Location::factory()
                ->withNameAndZipCode($zipCode, $name)
                ->create();
        }
    }
}
