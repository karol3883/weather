<?php

namespace App\Repository;

use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityRepository extends ServiceEntityRepository
{
    private const MAX_CITIES_EXCEPT_CITY_RESULT = 15;
    private const DEFAULT_CITIES_EXCEPT_CITY_RESULT = 5;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, City::class);
    }


    /**
     * Returns cities except city passed by
     * @param string $cityName
     * @return int|mixed|string
     */
    public function getCitiesExceptCity(string $cityName, int $limit = self::DEFAULT_CITIES_EXCEPT_CITY_RESULT)
    {
        $limit = min($limit, static::MAX_CITIES_EXCEPT_CITY_RESULT);

        $qb = $this->createQueryBuilder('u');
        $qb->where('u.name != :cityName')
            ->setParameter('cityName', $cityName)
            ->setMaxResults($limit);

        return $qb->getQuery()
            ->getResult();
    }
}
