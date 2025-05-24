<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public function index(): JsonResponse
    {
        $locations = Location::all()->map(function ($location) {
            return [
                'id' => $location->id,
                'name' => $location->name,
                'coordinates' => $location->coordinates,
            ];
        });

        return new JsonResponse($locations);
    }

    /**
     * Enregistre une nouvelle location
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'wkt' => 'required|string',
        ]);

        $location = Location::createWithWkt($validated['name'], $validated['wkt']);

        return response()->json($location, 201);
    }

    /**
     * Affiche une location spécifique
     */
    public function show(Location $location): JsonResponse
    {
        $location = Location::findOrFail($location->id);
        return new JsonResponse([
            'id' => $location->id,
            'name' => $location->name,
            'coordinates' => $location->coordinates,
        ]);
    }

    /**
     * Met à jour une location
     */
    public function update(Request $request, Location $location): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'latitude' => 'sometimes|required_with:longitude|numeric',
            'longitude' => 'sometimes|required_with:latitude|numeric',
        ]);

        $updates = [];

        if (isset($validated['name'])) {
            $updates['name'] = $validated['name'];
        }

        if (isset($validated['latitude']) && isset($validated['longitude'])) {
            $updates['geom'] = DB::raw("ST_SetSRID(ST_MakePoint({$validated['longitude']}, {$validated['latitude']}), 4326)");
        }

        $location->update($updates);

        return response()->json($location);
    }

    /**
     * Supprime une location
     */
    public function destroy(Location $location): JsonResponse
    {
        $location->delete();
        return response()->json(null, 204);
    }

    public function nearby(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'nullable|numeric',
        ]);

        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $radius = $request->input('radius', 10);

        $locations = DB::select("
        SELECT id, name,
               ST_X(geom) as lng,
               ST_Y(geom) as lat
        FROM locations
        WHERE ST_DWithin(
            geom::geography,
            ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography,
            ?
        )
    ", [$lng, $lat, $radius * 1000]);

        return response()->json($locations);
    }
}
