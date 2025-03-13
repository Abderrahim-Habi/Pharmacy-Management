<?php

namespace App\Repository;

use App\Entity\Medicament;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Medicament>
 */
class MedicamentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Medicament::class);
    }

    //    /**
    //     * @return Medicament[] Returns an array of Medicament objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Medicament
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
        public function findByCategory($categoryId)
        {
            return $this->createQueryBuilder('m')
                ->andWhere('m.Category = :category')
                ->setParameter('category', $categoryId)
                ->getQuery()
                ->getResult();
        }
        public function paginateMedicament(int $page,int $limit,$categoryId):Paginator{
            if($categoryId == null){
                return new Paginator($this->createQueryBuilder("m")
                    ->setFirstResult(($page - 1) * $limit)
                    ->setMaxResults($limit)
                    ->getQuery()
                    ->setHint(Paginator::HINT_ENABLE_DISTINCT, false),
                    false
                );
            }
            return new Paginator($this->createQueryBuilder("m")
                    ->setFirstResult(($page - 1) * $limit)
                    ->setMaxResults($limit)
                    ->andWhere('m.Category = :category')
                    ->setParameter('category', $categoryId) // Assuming category 1 is the medicament category
                    ->getQuery()
                    ->setHint(Paginator::HINT_ENABLE_DISTINCT, false),
                    false
                );
        }
}
