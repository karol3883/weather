<?php

namespace App\Service;

use App\Traits\Weather\WetaherApiTrait;
use App\Weather\WeatherFactory;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherApiService
{
    use WetaherApiTrait;

    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    /**
     *
     * Returns current temperature from passed $cityName base on weather APIs defined
     * in WetaherApiTrait::$listOfWeatherApiClasses
     *
     * @param string $cityName
     * @return float
     * @throws \Exception
     */
    public function getCurrentAverageTemperature(string $cityName): float
    {
        $weatherFactory = new WeatherFactory($cityName, $this->httpClient);

        $existingTemperatureData = 0;
        $temperaturesSum = 0;

        foreach ($this->listOfWeatherApiClasses as $apiClass) {
            try {
                $weatherApiInstance = $weatherFactory->getWeatherApiInstance($apiClass);

                if (is_numeric($temperature = $weatherApiInstance->getCurrentTemperature())) {
                    $temperaturesSum += $temperature;
                    $existingTemperatureData++;
                }
            } catch (\Exception $exception) {
            }
        }

        if ($existingTemperatureData === 0) {
            throw new \Exception("Data not exists, propably wrong city name passed");
        }

        return round($temperaturesSum / $existingTemperatureData, 2);
    }
}
