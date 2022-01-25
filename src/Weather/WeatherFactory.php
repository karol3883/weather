<?php

namespace App\Weather;


use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherFactory
{
    public function __construct(
        private string $cityName,
        private HttpClientInterface $httpClient,
    )
    {

    }

    public function getWeatherApiInstance(string $className): WeatherAbstract
    {
        return new $className($this->cityName, $this->httpClient);
    }
}