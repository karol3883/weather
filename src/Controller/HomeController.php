<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Weather;
use App\Service\WeatherApiService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
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
        $cityEntity = $entityManager->getRepository(City::class);

        $city = $entityManager->getRepository(City::class)->findBy(['name' => $cityName]);

        try {
            $temperature = $weatherApiService->getCurrentAverageTemperature($cityName);
        } catch (\Exception $exception) {
            throw $this->createNotFoundException(sprintf('Nie znaleziono temperatury dla miasta %s', $cityName), $exception);
        }

        if (!$cityEntity->findBy(['name' => $cityName])) {

            $city = new City();
            $city->setName($cityName);

            $entityManager->persist($city);
            $entityManager->flush();
        }

        $city = $cityEntity->findBy(['name' => $cityName]);

        $weather = new Weather();
        $weather->setCity($city[0]);
        $weather->setCreateAt(new \DateTimeImmutable());
        $weather->setTemperature($temperature);

        $entityManager->persist($weather);
        $entityManager->flush();

//        $cache = new FilesystemAdapter();
//        $value = $cache->get('my_cache_key', function (ItemInterface $item) use($entityManager, $cityName) {
//            $item->expiresAfter(10);
//
//
//            echo "cache";
//            return $entityManager->getRepository(Weather::class)->findBy(['city' => 'Rusinow']);
//        });

//        dd($entityManager->getRepository(Weather::class)->getTemperaturesFromCityGroupByHours($cityName));
//        dd($cityName);

        return $this->render('home/show_city_temperature.html.twig', [
            'temperature' => $temperature,
            'cityName' => $cityName,
        ]);
    }

}
