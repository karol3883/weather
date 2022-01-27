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

use App\Traits\Cities\CacheTrait;
use App\Traits\Weather\WeatherTrait;
use App\Constants\TimeConstants;

class WeatherController extends AbstractController
{
    use CacheTrait;
    use WeatherTrait;

    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', []);
    }

    #[Route('/show_city_temperature/{cityNameSlug?}', name: 'show_city_temperature')]
    public function showDataFromApi(
        string $cityNameSlug,
        Request $request,
        EntityManagerInterface $entityManager,
        WeatherApiService $weatherApiService
    ): Response {
        $cityName =  $this->getCityNameFromRequest($request, $cityNameSlug);

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

        $cache = new FilesystemAdapter();
        $value = $cache->get("my_cache_key$cityName", function (ItemInterface $item) use ($entityManager, $cityName) {
            $item->expiresAfter(TimeConstants::MINUTE * 30);


            return $entityManager->getRepository(Weather::class)->getTemperaturesFromCityGroupByHours($cityName);
        });


        return $this->render(
            'home/show_city_temperature.html.twig',
            [
                'currentTemperature' => $temperature,
                'cityName' => $cityName,
                'temperatures' => $value,
            ]
        );
    }

    #[Route('/check_current_temperature', name: 'check_current_temperature')]
    public function checkCurrentTemperature(
        Request $request,
        EntityManagerInterface $entityManager,
        WeatherApiService $weatherApiService
    ): Response {
        return $this->render(
            'home/show_city_temperature.html.twig',
            [
                'currentTemperature' => 1,
                'cityName' => 123,
                'temperatures' => 12345,
            ]
        );
    }

    public function otherAvailableCities(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $cityName = $this->getCityNameFromRequest($request);
        $cache = new FilesystemAdapter();
        $otherAvailableCities = $cache->get("{$this->available_cities_cache_key}$cityName", function (ItemInterface $item) use ($entityManager, $cityName) {
            $item->expiresAfter(TimeConstants::HOUR);

            return  $entityManager->getRepository(City::class)->getCitiesExceptCity($cityName);
        });

        return $this->render('home/other_available_cities_list.html.twig', [
            'other_available_cities' => $otherAvailableCities
        ]);
    }
}
