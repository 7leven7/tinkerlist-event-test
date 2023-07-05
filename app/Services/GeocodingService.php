<?php


namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeocodingService
{
    private $apiKey;
    private $apiUrl;

    public function __construct()
    {
        $this->apiKey = getenv('GEOCODING_NINJA_API_KEY');
        $this->apiUrl = getenv('GEOCODING_NINJA_BASE_URL');
    }

    public function getCoordinatesByCityName($cityName,$countryCode)
    {
        $response = Http::withHeaders([
            'X-Api-Key' => $this->apiKey,
        ])->get($this->apiUrl, [
            'city' => $cityName,
            'country' => $countryCode
        ]);

        if ($response->successful()) {
            return $response->json();
        } else {
            return "Error: " . $response->status() . " " . $response->body();
        }
    }
}
