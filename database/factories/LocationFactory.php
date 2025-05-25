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

    public function withNameAndZipCode(string $zipCode, string $name): static
    {
        $cityData = GeoGouvHelper::cityByNameAndZipCode($name, $zipCode);

        if (!$cityData) {
            throw new \Exception("City not found for name $name and zip code $zipCode");
        }

        return $this->state(function () use ($name) {
            return [
                'name' => $name,
            ];
        })->afterCreating(function (Location $location) use ($cityData) {
            DB::update('UPDATE locations SET geom = ST_SetSRID(ST_MakePoint(?, ?), 4326) WHERE id = ?', [
                $cityData['lng'],
                $cityData['lat'],
                $location->id,
            ]);
        });
    }
}
