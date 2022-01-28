<?php

declare(strict_types=1);

namespace App\Traits\Cities;

use App\Weather;

trait CacheTrait
{
    private string $available_cities_cache_key = 'other_available_cities_';

    private string $city_temperature_cache_key = 'city_temperature_cache_key';
}
