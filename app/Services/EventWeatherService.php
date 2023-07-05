<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class EventWeatherService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = getenv('WEATHER_API_KEY');
        $this->baseUrl = getenv('WEATHER_BASE_URL');
    }

    public function getWeatherForecast(float $latitude, float $longitude)
    {
        $url = $this->buildUrl($latitude, $longitude);

        $response = Http::get($url);

        if ($response->failed()) {
            throw new \Exception('Failed to retrieve weather data.');
        }

        return $response->json();
    }

    protected function buildUrl(float $latitude, float $longitude): string
    {
    
        $queryParams = [
            'lat' => $latitude,
            'lon' => $longitude,
            'units' => 'metric',
            'exclude' => 'minutely',
            'appid' => $this->apiKey,
        ];

        return $this->baseUrl . '?' . http_build_query($queryParams);
    }
}
