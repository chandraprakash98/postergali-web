<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GeocodeService
{
    /**
     * Convert latitude and longitude to full address using Nominatim (OpenStreetMap)
     */
    public static function getAddress($latitude, $longitude)
    {
        if (!$latitude || !$longitude) {
            return null;
        }

        // Create a cache key
        $cacheKey = "address_" . md5($latitude . $longitude);

        // Check if address is cached
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::timeout(10)->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $latitude,
                'lon' => $longitude,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $address = $data['address'] ?? [];
                
                // Build a readable address
                $addressParts = [];
                
                // Include relevant parts
                if (!empty($address['house_number'])) {
                    $addressParts[] = $address['house_number'];
                }
                if (!empty($address['road'])) {
                    $addressParts[] = $address['road'];
                }
                if (!empty($address['suburb'])) {
                    $addressParts[] = $address['suburb'];
                }
                if (!empty($address['city'])) {
                    $addressParts[] = $address['city'];
                } elseif (!empty($address['town'])) {
                    $addressParts[] = $address['town'];
                }
                if (!empty($address['state'])) {
                    $addressParts[] = $address['state'];
                }
                if (!empty($address['postcode'])) {
                    $addressParts[] = $address['postcode'];
                }
                if (!empty($address['country'])) {
                    $addressParts[] = $address['country'];
                }

                $fullAddress = implode(', ', array_filter($addressParts));

                // Cache for 30 days
                Cache::put($cacheKey, $fullAddress, now()->addDays(30));

                return $fullAddress;
            }
        } catch (\Exception $e) {
            \Log::error('Geocode Error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get coordinates from address
     */
    public static function getCoordinates($address)
    {
        if (!$address) {
            return null;
        }

        $cacheKey = "coords_" . md5($address);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::timeout(10)->get('https://nominatim.openstreetmap.org/search', [
                'q' => $address,
                'format' => 'json',
                'limit' => 1,
            ]);

            if ($response->successful() && !empty($response->json())) {
                $data = $response->json()[0];
                $coords = [
                    'lat' => $data['lat'],
                    'lon' => $data['lon']
                ];

                Cache::put($cacheKey, $coords, now()->addDays(30));
                return $coords;
            }
        } catch (\Exception $e) {
            \Log::error('Geocode Error: ' . $e->getMessage());
        }

        return null;
    }
}
