<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class EventWeatherService
{
    /**
     * @var array|false|string
     */
    protected string|array|false $apiKey;

    /**
     * @var string|array|false
     */
    protected string|array|false $baseUrl;

    public function __construct()
    {
        $this->apiKey = getenv('WEATHER_API_KEY');
        $this->baseUrl = getenv('WEATHER_BASE_URL');
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @return array|mixed
     * @throws Exception
     */
    public function getWeatherForecast(float $latitude, float $longitude): mixed
    {
        $url = $this->buildUrl($latitude, $longitude);

        $response = Http::get($url);

        if ($response->failed()) {
            throw new Exception('Failed to retrieve weather data.');
        }

        return $response->json();
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @return string
     */
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
