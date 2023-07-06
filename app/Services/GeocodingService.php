<?php


namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeocodingService
{
    /**
     * @var string|array|false
     */
    private string|array|false $apiKey;

    /**
     * @var array|false|string
     */
    private string|array|false $apiUrl;

    public function __construct()
    {
        $this->apiKey = getenv('GEOCODING_NINJA_API_KEY');
        $this->apiUrl = getenv('GEOCODING_NINJA_BASE_URL');
    }

    /**
     * @param $cityName
     * @param $countryCode
     * @return array|mixed|string
     */
    public function getCoordinatesByCityName($cityName, $countryCode)
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
