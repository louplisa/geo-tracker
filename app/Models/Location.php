<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Location extends Model
{
    protected $fillable = [
        'name',
    ];

    protected $hidden = ['geom'];

    protected $appends = ['coordinates', 'geom_wkt'];

    public function getCoordinatesAttribute(): ?array
    {
        if (!$this->exists) return null;

        $point = DB::selectOne("SELECT ST_X(geom) AS lng, ST_Y(geom) AS lat FROM locations WHERE id = ?", [$this->id]);

        return $point ? ['lng' => (float) $point->lng, 'lat' => (float) $point->lat] : null;
    }

    // Accessor to retrieve 'geom' in WKT (Well-Known Text) format
    public function getGeomWktAttribute(): ?string
    {
        if (!$this->exists) return null;

        $result = DB::selectOne("SELECT ST_AsText(geom) AS wkt FROM locations WHERE id = ?", [$this->id]);

        return $result ? $result->wkt : null;
    }

    // Mutator to set 'geom' from a WKT
    public function setGeomFromWkt(string $wkt): static
    {
        if (!$this->exists) {
            throw new \LogicException("Impossible de définir la géométrie : l'objet n'existe pas encore en base.");
        }

        DB::update("UPDATE locations SET geom = ST_GeomFromText(?, 4326) WHERE id = ?", [$wkt, $this->id]);

        return $this->refresh();
    }

    // Creation of a Location with name and geom WKT
    public static function createWithWkt(string $name, string $wkt): Location
    {
        $location = new static();
        $location->name = $name;
        $location->save();

        // Mise à jour séparée avec requête préparée
        DB::update('UPDATE locations SET geom = ST_GeomFromText(?, 4326) WHERE id = ?', [
            $wkt,
            $location->id,
        ]);

        return $location->refresh();
    }
}
