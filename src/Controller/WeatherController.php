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

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/show_city_temperature/{cityNameSlug?}', name: 'show_city_temperature')]
    public function showDataFromApi(
        ?string $cityNameSlug,
        Request $request,
        EntityManagerInterface $entityManager,
        WeatherApiService $weatherApiService
    ): Response {
        $cityName =  $this->getCityNameFromRequest($request, $cityNameSlug);

        if (!$cityName) {
            $this->addFlash('danger', 'Brak nazwy miasta!');
            return $this->redirect('/');
        }

        $cache = new FilesystemAdapter();
        try {
            $temperature = $cache->get("{$this->city_temperature_cache_key}$cityName", function (ItemInterface $item) use ($weatherApiService, $cityName, $entityManager) {
                $item->expiresAfter(TimeConstants::MINUTE * 5);

                $cityEntity = $entityManager->getRepository(City::class);
                if (!$cityEntity->findBy(['name' => $cityName])) {
                    $city = new City();
                    $city->setName($cityName);

                    $entityManager->persist($city);
                    $entityManager->flush();
                }

                $city = $cityEntity->findBy(['name' => $cityName]);

                $temperature = $weatherApiService->getCurrentAverageTemperature($cityName);
                $weather = new Weather();
                $weather->setCity($city[0]);
                $weather->setCreateAt(new \DateTimeImmutable());
                $weather->setTemperature($temperature);

                $entityManager->persist($weather);
                $entityManager->flush();

                return $temperature;
            });
        } catch (\Exception $exception) {
            throw $this->createNotFoundException(sprintf('Nie znaleziono temperatury dla miasta %s', $cityName), $exception);
        }

        $temperatures = $cache->get(
            "{$this->city_temperature_details_cache_key}$cityName",
            function (ItemInterface $item) use ($entityManager, $cityName) {
                $item->expiresAfter(TimeConstants::MINUTE * 30);
                return $entityManager->getRepository(Weather::class)->getTemperaturesFromCityGroupByHours($cityName);
            }
        );

        return $this->render(
            'home/show_city_temperature.html.twig',
            [
                'currentTemperature' => $temperature,
                'cityName' => $cityName,
                'temperatures' => $temperatures,
            ]
        );
    }

    public function otherAvailableCities(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $cityName = $this->getCityNameFromRequest($request);
        $cache = new FilesystemAdapter();

        $otherAvailableCities = $cache->get(
            "{$this->available_cities_cache_key}$cityName",
            function (ItemInterface $item) use ($entityManager, $cityName) {
                $item->expiresAfter(TimeConstants::HOUR);

                return $entityManager->getRepository(City::class)->getCitiesExceptCity($cityName);
            }
        );

        return $this->render(
            'home/other_available_cities_list.html.twig',
            [
                'other_available_cities' => $otherAvailableCities
            ]
        );
    }
}
