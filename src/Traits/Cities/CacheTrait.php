<?php

declare(strict_types=1);

namespace App\Traits\Cities;

trait CacheTrait
{
    private string $available_cities_cache_key = 'other_available_cities';

    private string $city_temperature_cache_key = 'city_temperature';

    private string $city_temperature_details_cache_key = 'city_temperature_details';
}
