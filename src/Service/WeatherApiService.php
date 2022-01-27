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

        $existingTemperatureData = 0;
        $temperaturesSum = 0;

        foreach ($this->listOfWeatherApiClasses as $apiClass) {

            try {
                $weatherApiInstance = $weatherFactory->getWeatherApiInstance($apiClass);
                $temperature = $weatherApiInstance->getCurrentTemperature();

                if (is_numeric($temperature = $weatherApiInstance->getCurrentTemperature()))  {
                    $temperaturesSum += $temperature;
                    $existingTemperatureData++;
                }

            } catch (\Exception $exception) {

            }
        }

        if ($existingTemperatureData === 0) {
            throw new \Exception("No data from any api");
        }


        return round($temperaturesSum / $existingTemperatureData, 2);
    }
}