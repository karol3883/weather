<?php

namespace App\Weather;

use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class WeatherAbstract
{

    const API_METHOD_TYPE = 'GET';

    public function __construct(
        protected string $cityName,
        private HttpClientInterface $httpClient
    )
    {
    }

    protected string $apiKey;

    protected string $apiUrl;

    protected function getData(): iterable
    {
        if (!$this->isPossibleToUseApi()) {
            return [];
        }

        $response = $this->httpClient->request(
            static::API_METHOD_TYPE,
            $this->apiUrl,
            $this->getApiCallParameters()
        );

        return $response->toArray();
    }

    private function isPossibleToUseApi(): bool
    {
        return $this->apiUrl && $this->apiKey;
    }


    abstract protected function getApiCallParameters(): array;

    abstract public function getCurrentTemperature(): mixed;
}