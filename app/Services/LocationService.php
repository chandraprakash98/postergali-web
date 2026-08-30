<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class LocationService
{
    /**
     * Earth's radius in kilometers
     */
    const EARTH_RADIUS_KM = 6371;

    /**
     * Calculate distance between two coordinates in kilometers using Haversine formula
     */
    public static function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return self::EARTH_RADIUS_KM * $c;
    }

    /**
     * Build a query to find records within a certain distance of coordinates
     * 
     * @param Builder $query
     * @param float $latitude User's latitude
     * @param float $longitude User's longitude
     * @param float $radiusKm Search radius in kilometers (default 5 km)
     * @param string $latColumn Latitude column name (default 'latitude')
     * @param string $lonColumn Longitude column name (default 'longitude')
     */
    public static function nearbyQuery(
        Builder $query,
        float $latitude,
        float $longitude,
        float $radiusKm = 5,
        string $latColumn = 'latitude',
        string $lonColumn = 'longitude',
        float $minRadiusKm = 0
    ): Builder {
        $connection = $query->getQuery()->getConnection();
        $driver = $connection->getDriverName();

        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            $bounds = self::getBoundingBox($latitude, $longitude, $radiusKm);

            $query
                ->whereBetween($latColumn, [$bounds['minLat'], $bounds['maxLat']])
                ->whereBetween($lonColumn, [$bounds['minLon'], $bounds['maxLon']]);

            if ($minRadiusKm > 0) {
                $query->whereRaw("((($latColumn - ?) * 111.0) * (($latColumn - ?) * 111.0) + (($lonColumn - ?) * 111.0) * (($lonColumn - ?) * 111.0)) >= ?", [
                    $latitude, $latitude, $longitude, $longitude, $minRadiusKm * $minRadiusKm
                ]);
            }

            return $query
                ->selectRaw('0 AS distance')
                ->orderBy('distance', 'asc');
        }

        $table = $query->getModel()->getTable();
        $latCol = $table . '.' . $latColumn;
        $lonCol = $table . '.' . $lonColumn;

        // Calculate distance using Haversine formula and filter within radius
        $distanceFormula = "ROUND(
            (
                " . self::EARTH_RADIUS_KM . " * acos(
                    cos(radians(?))
                    * cos(radians($latCol))
                    * cos(radians($lonCol) - radians(?))
                    + sin(radians(?))
                    * sin(radians($latCol))
                )
            ), 2
        )";

        if ($minRadiusKm > 0) {
            $query->whereRaw(
                $distanceFormula . " >= ?",
                [$latitude, $longitude, $latitude, $minRadiusKm]
            );
        }

        // Use where clause instead of having to work with pagination
        return $query->whereRaw(
            $distanceFormula . " <= ?",
            [$latitude, $longitude, $latitude, $radiusKm]
        )
        ->selectRaw($distanceFormula . " AS distance", [$latitude, $longitude, $latitude])
        ->orderBy('distance', 'asc');
    }

    /**
     * Bounding box coordinates for initial filtering (optimization)
     * Returns an array with [minLat, maxLat, minLon, maxLon]
     */
    public static function getBoundingBox(float $latitude, float $longitude, float $radiusKm): array
    {
        // 1 degree of latitude is approximately 111 km
        // 1 degree of longitude varies by latitude
        
        $latOffset = $radiusKm / 111;
        $lonOffset = $radiusKm / (111 * cos(deg2rad($latitude)));

        return [
            'minLat' => $latitude - $latOffset,
            'maxLat' => $latitude + $latOffset,
            'minLon' => $longitude - $lonOffset,
            'maxLon' => $longitude + $lonOffset,
        ];
    }

    /**
     * Apply bounding box filtering for performance optimization
     * Use this before applying the full Haversine calculation
     */
    public static function withBoundingBox(
        Builder $query,
        float $latitude,
        float $longitude,
        float $radiusKm = 5,
        string $latColumn = 'latitude',
        string $lonColumn = 'longitude'
    ): Builder {
        $bounds = self::getBoundingBox($latitude, $longitude, $radiusKm);

        return $query
            ->whereBetween($latColumn, [$bounds['minLat'], $bounds['maxLat']])
            ->whereBetween($lonColumn, [$bounds['minLon'], $bounds['maxLon']]);
    }
}
