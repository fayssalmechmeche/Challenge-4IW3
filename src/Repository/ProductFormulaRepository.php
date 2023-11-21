<?php

namespace App\Repository;

use App\Entity\ProductFormula;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductFormula>
 *
 * @method ProductFormula|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductFormula|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductFormula[]    findAll()
 * @method ProductFormula[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductFormulaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductFormula::class);
    }

//    /**
//     * @return ProductFormula[] Returns an array of ProductFormula objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ProductFormula
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
