<?php

declare(strict_types=1);

namespace App\Traits\Weather;

use App\Weather;

trait WetaherApiTrait
{
    private array $listOfWeatherApiClasses = [
        Weather\OpenWeatherMap::class,
        Weather\WeatherApiCom::class,
    ];
}
