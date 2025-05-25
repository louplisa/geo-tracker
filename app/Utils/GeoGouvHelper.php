<?php

namespace App\Utils;

use Illuminate\Support\Facades\Http;

class GeoGouvHelper
{
    public static function cityByNameAndZipCode(string $name, string $zipCode): ?array
    {
        $response = Http::get('https://geo.api.gouv.fr/communes', [
            'codePostal' => $zipCode,
            'nom' => $name,
            'fields' => 'nom,centre',
            'format' => 'json',
            'geometry' => 'centre',
        ]);

        $city = $response->json();

        if (empty($city)) {
            return null;
        }

        return [
            'name' => $city[0]['nom'],
            'lat' => $city[0]['centre']['coordinates'][1],
            'lng' => $city[0]['centre']['coordinates'][0],
        ];
    }
}
