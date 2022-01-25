<?php

namespace App\Service;

use App\Traits\ApiTrait;
use App\Weather\WeatherFactory;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherApiService
{
    use ApiTrait;

    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    /**
     * @throws \Exception
     */
    public function getCurrentAverageTemperature(string $cityName): mixed
    {

        $weatherFactory = new WeatherFactory($cityName, $this->httpClient);

        $iterator = 0;
        $existingTemperatureData = 0;
        $temperaturesSum = 0;

        foreach ($this->listOfWeatherApiClasses as $apiClass) {
            $temperaturesSum += $weatherFactory->getWeatherApiInstance($apiClass)->getCurrentTemperature();
            $existingTemperatureData++;
        }
        var_dump($existingTemperatureData);

        return round($temperaturesSum / $existingTemperatureData, 2);
    }
}