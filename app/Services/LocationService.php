<?php

namespace App\Services;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LocationService
{
    /**
     * Create a location with WKT and associate a user (if provided)
     *
     * @param string $name
     * @param string $wkt
     * @param int|null $userId
     * @return Location
     '
     */
    public function createWithWktAndUser(string $name, string $wkt, ?int $userId = null): Location
    {
        $location = Location::createWithWkt($name, $wkt);

        $this->addUserToLocation($location, $userId);

        return $location;
    }


    /**
     * Create a location based on city name and zip code, optionally associating a user.
     *
     * @param string $name
     * @param string $zipCode
     * @param int|null $userId
     * @return Location|null
     */
    public function createFromNameAndZipCode(string $name, string $zipCode, ?int $userId = null): ?Location
    {
        $city = app('App\Utils\GeoGouvHelper')->cityByNameAndZipCode($name, $zipCode);
        if (empty($city)) {
            return null;
        }

        $location = Location::createWithWkt($city['name'], "POINT({$city['lng']} {$city['lat']})");

        $this->addUserToLocation($location, $userId);

        return $location;
    }

    /**
     * Associate a user with the given location if a user ID is provided.
     *
     * @param Location $location
     * @param int|null $userId
     * @return void*
     */
    private function addUserToLocation(Location $location, ?int $userId): void
    {
        if (!$userId) {
            return;
        }
        $user = User::findOrFail($userId);
        $location->locatable()->associate($user);
        $location->save();
    }
}
