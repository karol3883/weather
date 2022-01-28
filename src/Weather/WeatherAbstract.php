<?php

namespace App\Weather;

use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class WeatherAbstract
{
    protected string $apiKey;

    protected string $apiUrl;

    protected iterable $responseData = [];

    public const API_METHOD_TYPE = 'GET';

    public function __construct(
        protected string $cityName,
        private HttpClientInterface $httpClient
    ) {
        $this->setResponseData();
    }

    protected function setResponseData(): void
    {
        $response = $this->httpClient->request(
            static::API_METHOD_TYPE,
            $this->apiUrl,
            $this->getApiCallParameters()
        );

        $this->responseData = $response->toArray();
    }

    abstract protected function getApiCallParameters(): array;

    abstract public function getCurrentTemperature(): mixed;
}
