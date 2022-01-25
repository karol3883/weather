<?php

namespace App\Controller;

use App\Entity\City;
use App\Service\WeatherApiService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HomeController extends AbstractController
{

    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
//        $resp = $this->httpClient->request(
//            'GET',
//            'http://api.openweathermap.org/data/2.5/weather',
//            [
//                'query' => [
//                    'appid' => '667c2aa615ea3a4f8ee28e4f58c8e335',
//                    'q' => 'Lublin',
//                ],
//            ]
//        );
//
//        $resp = $this->httpClient->request(
//            'GET',
//            'http://api.weatherapi.com/v1/forecast.json',
//            [
//                'query' => [
//                    'key' => 'aaa3889517594fa7a97175112222501',
//                    'q' => 'Lublin',
//                ],
//            ]
//        );
//
//        $content = $resp->getContent();
//        $content = $resp->toArray();
//        dd($content);
//        dd($resp);

        return $this->render('home/index.html.twig', []);
    }

    #[Route('/show_city_temperature', name: 'show_city_temperature')]
    public function showDataFromApi(
        Request $request,
        EntityManagerInterface $entityManager,
        WeatherApiService $weatherApiService
    ): Response
    {
        $cityName = ucfirst(strtolower($request->get('city')));
//        $entityManager->getRepository(City::class)->createCityIfNotExtists($cityName);
        $city = $entityManager->getRepository(City::class)->findBy(['name' => $cityName]);

        try {
            $temperature = $weatherApiService->getCurrentAverageTemperature($cityName);
        } catch (\Exception $exception) {
            echo "dupa";
        }

        return $this->render('home/show_city_temperature.html.twig', [
            'temperature' => $temperature,
            'cityName' => $cityName,
        ]);
    }
}
