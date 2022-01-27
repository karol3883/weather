<?php

namespace App\Weather;

class OpenWeatherMap extends WeatherAbstract
{

    protected string $apiKey = '667c2aa615ea3a4f8ee28e4f58c8e335';

    protected string $apiUrl = 'https://api.openweathermap.org/data/2.5/weather';

    protected function getApiCallParameters(): array
    {
        return [
            'query' => [
                'appid' => $this->apiKey,
                'q' => $this->cityName,
            ],
        ];
    }

    public function getCurrentTemperature(): ?float
    {
        return !empty($this->responseData['main']['temp']) ?
            round($this->responseData['main']['temp'] - 273.15,2) :
            null;
    }
}