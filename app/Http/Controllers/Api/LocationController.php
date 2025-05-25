<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public function __construct(private LocationService $locationService)
    {
    }

    /**
     * Get a list of all locations including their associated locatable entities (e.g. users).
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $locations = Location::with('locatable')->get();

        return response()->json(LocationResource::collection($locations));
    }

    /**
     * Store a new location with WKT (Well-Known Text) geometry and optionally associate a user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:locations,name',
            'wkt' => 'required|string',
            'user' => 'nullable|integer|exists:users,id'
        ]);

        $location = $this->locationService->createWithWktAndUser(
            $validated['name'],
            $validated['wkt'],
            $validated['user'] ?? null
        );

        return response()->json($location, 201);
    }

    /**
     * Store a new location based on city name and zip code, optionally associating a user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function storeByNameAndZipCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'zipCode' => 'required|string|max:5|min:5',
            'user' => 'nullable|integer|exists:users,id'
        ]);

        $location = $this->locationService->createFromNameAndZipCode(
            $validated['name'],
            $validated['zipCode'],
            $validated['user'] ?? null
        );

        if (!$location) {
            return response()->json(['message' => 'Aucune ville trouvée pour ce code postal'], 404);
        }

        return response()->json($location, 201);
    }

    /**
     * Display a specific location with its associated locatable entity.
     *
     * @param Location $location
     * @return JsonResponse
     */
    public function show(Location $location): JsonResponse
    {
        $location->load('locatable');

        return response()->json(new LocationResource($location));
    }

    /**
     * Update an existing location's name and/or geographical coordinates.
     *
     * @param Request $request
     * @param Location $location
     * @return JsonResponse
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
            $updates['geom'] = DB::statement('UPDATE locations SET geom = ST_SetSRID(ST_MakePoint(?, ?), 4326) WHERE id = ?', [
                $validated['longitude'], $validated['latitude'], $location->id
            ]);
        }

        $location->update($updates);

        return response()->json($location);
    }

    /**
     * Delete the specified location.
     *
     * @param Location $location
     * @return JsonResponse
     */
    public function destroy(Location $location): JsonResponse
    {
        $location->delete();
        return response()->json(null, 204);
    }

    /**
     * Find locations near a given latitude and longitude within a specified radius (default 10km).
     * Uses raw SQL with PostGIS functions for geographic queries.
     *
     * @param Request $request
     * @return JsonResponse
     */
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
