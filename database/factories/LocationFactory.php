<?php

namespace Database\Factories;

use App\Models\Location;
use App\Utils\GeoGouvHelper;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->city,
            'locatable_type' => null,
            'locatable_id' => null,
        ];
    }

    public function withPostalCode(string $postalCode): static
    {
        return $this->afterCreating(function (Location $location) use ($postalCode) {
            $cityData = GeoGouvHelper::cityByPostalCode($postalCode);

            if ($cityData) {
                DB::update('UPDATE locations SET geom = ST_SetSRID(ST_MakePoint(?, ?), 4326) WHERE id = ?', [
                    $cityData['lng'],
                    $cityData['lat'],
                    $location->id,
                ]);

                // Mettre à jour le nom si nécessaire
                $location->name = $cityData['name'];
                $location->save();
            }
        });
    }
}
