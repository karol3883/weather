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

    public function getCurrentTemperature(): mixed
    {
        $responseData = $this->getData();

        if (empty($responseData['current']['temp_c'])) {
            throw new \Exception(sprintf('Temperature not exists. Propably api data has been changed'));
        }

        return (float) $responseData['current']['temp_c'];
    }
}