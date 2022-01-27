<?php

declare(strict_types=1);

namespace App\Traits\Weather;

use App\Weather;
use Symfony\Component\HttpFoundation\Request;

trait WeatherTrait
{
    private string $available_cities_cache_key = 'other_available_cities_';

    /**
     * @return string
     */
    private function getCityNameFromRequest(Request $request, ?string $cityNameSlug = null): string
    {
        $cityName = $request->get('city') ?? $cityNameSlug;
        return ucfirst(strtolower($cityName));
    }
}
