<?php

namespace App\Repository;

use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Serie>
 */
class SerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Serie::class);
    }


    public function findSeriesByGenre(string $genre, ?string $status = null): array
    {
        $q = $this->createQueryBuilder('s')
            ->orderBy('s.firstAirDate', 'DESC')
            ->andWhere('s.genres like :genre')
            ->setParameter(':genre', '%'.$genre.'%');

        if ($status) {
            $q->andWhere('s.status = :status')
                ->setParameter(':status', $status);
        }

        return $q->getQuery()
            ->getResult();
    }

    public function findSeriesByGenreWithDql(string $genre, string $status): array
    {
        $dql = "SELECT s FROM App\Entity\Serie s 
        WHERE s.genres like :genre 
        AND s.status = :status 
        ORDER BY s.firstAirDate DESC";

        return $this->getEntityManager()->createQuery($dql)
            ->setParameter(':genre', '%'.$genre.'%')
            ->setParameter(':status', $status)
            ->execute();
    }


    public function findSeriesByGenreWithRawSql(string $genre, string $status): array
    {
        $sql = "SELECT * FROM serie s 
         WHERE s.genres LIKE :genre AND s.status = :status
        ORDER BY s.first_air_date DESC";

        $conn = $this->getEntityManager()->getConnection();
        return $conn->prepare($sql)
            ->executeQuery([':genre' => $genre, ':status' => $status])
            ->fetchAllAssociative();

    }



    //    /**
    //     * @return Serie[] Returns an array of Serie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Serie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
