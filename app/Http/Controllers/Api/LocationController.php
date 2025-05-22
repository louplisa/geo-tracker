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
}
