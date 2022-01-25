<?php

declare(strict_types=1);

namespace App\Traits;

use App\Weather;

trait ApiTrait
{
    private array $listOfWeatherApiClasses = [
        Weather\OpenWeatherMap::class,
        Weather\WeatherApiCom::class,
    ];
}