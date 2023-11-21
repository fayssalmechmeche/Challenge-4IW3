<?php

namespace App\Repository;

use App\Entity\DevisProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DevisProduct>
 *
 * @method DevisProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method DevisProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method DevisProduct[]    findAll()
 * @method DevisProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevisProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DevisProduct::class);
    }

//    /**
//     * @return DevisProduct[] Returns an array of DevisProduct objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DevisProduct
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
