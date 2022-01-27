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
               DATE_FORMAT(w.create_at, '%Y-%m-%d %H:%i') as date
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
                DATE_FORMAT(w.create_at, '%Y-%m-%d %H') ASC  
            LIMIT
                100
        ";


        echo "<pre>";
        var_dump($connection->prepare($sql)->executeQuery([':val' => $cityName])->fetchAllAssociative());
        echo "</pre>";
        return $connection->prepare($sql)->executeQuery([':val' => $cityName])->fetchAllAssociative();
    }

    // /**
    //  * @return Weather[] Returns an array of Weather objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Weather
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
