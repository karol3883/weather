<?php

namespace App\Weather;

class WeatherApiCom extends WeatherAbstract
{

    protected string $apiKey = 'aaa3889517594fa7a97175112222501';

    protected string $apiUrl = 'http://api.weatherapi.com/v1/forecast.json';

    protected function getApiCallParameters(): array
    {
        return [
            'query' => [
                'key' => $this->apiKey,
                'q' => $this->cityName,
            ],
        ];
    }

    public function getCurrentTemperature(): ?float
    {
        return !empty($this->responseData['current']['temp_c']) ? (float) $this->responseData['current']['temp_c'] : null;
    }
}