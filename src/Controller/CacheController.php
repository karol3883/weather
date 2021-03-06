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

class CacheController extends AbstractController
{
    #[Route('/recache_application', name: 'recache_app')]
    public function index(Request $request): Response
    {
        $cache = new FilesystemAdapter();
        $cache->clear();

        $this->addFlash('success', 'Wykonano przekeszowanie');

        return $this->redirect($request->headers->get('referer'));
    }
}
