<?php

namespace App\Weather;

class OpenWeatherMap extends WeatherAbstract
{

    protected string $apiKey = '667c2aa615ea3a4f8ee28e4f58c8e335';

    protected string $apiUrl = 'https://api.openweathermap.org/data/2.5/weather';

    protected function getApiKey(): string
    {
        return $this->apiKey;
    }

    protected function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    protected function getApiCallParameters(): array
    {

        return [
            'query' => [
                'appid' => $this->getApiKey(),
                'q' => $this->cityName,
            ],
        ];
    }

    public function getCurrentTemperature(): mixed
    {
        $apiData = $this->getData();
        return !empty($apiData['main']['temp']) ? round($apiData['main']['temp'] - 273.15, 3) : null;
    }
}