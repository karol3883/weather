<?php

namespace App\Repository;

use App\Entity\Weather;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Weather|null find($id, $lockMode = null, $lockVersion = null)
 * @method Weather|null findOneBy(array $criteria, array $orderBy = null)
 * @method Weather[]    findAll()
 * @method Weather[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WeatherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Weather::class);
    }

    public function getTemperaturesFromCityGroupByHours(string $cityName)
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "
            SELECT
                c.name,
                w.temperature,
               CONCAT(DATE_FORMAT(w.create_at, '%Y-%m-%d %H'), ':00') as date
            FROM
                weather w   
            JOIN
                city c ON (c.id = w.city_id)
            WHERE
                c.name = :val
            GROUP BY
                c.name,
                w.temperature,
                DATE_FORMAT(w.create_at, '%Y-%m-%d %H')
            ORDER BY
                DATE_FORMAT(w.create_at, '%Y-%m-%d %H') DESC  
            LIMIT
                100
        ";

        return $connection->prepare($sql)->executeQuery([':val' => $cityName])->fetchAllAssociative();
    }
}
