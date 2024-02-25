<?php

namespace App\Repository;

use App\Entity\DevisProduct;
use App\Entity\Society;
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

    public function findMostSoldProductBySociety(Society $society)
    {
        return $this->createQueryBuilder('dp')
            ->select('p.name', 'SUM(dp.quantity) as totalQuantity', 'SUM(dp.price) as totalSpending')
            ->innerJoin('dp.product', 'p')
            ->where('dp.devis IN (
                SELECT d.id FROM App\Entity\Devis d
                WHERE d.society = :societyId
            )')
            ->groupBy('p.id')
            ->orderBy('totalQuantity', 'DESC')
            ->setMaxResults(1)
            ->setParameter('societyId', ['society' => $society->getId()])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findLessSoldProductBySociety(Society $society)
    {
        return $this->createQueryBuilder('dp')
            ->select('p.name', 'SUM(dp.quantity) as totalQuantity', 'SUM(dp.price) as totalSpending')
            ->innerJoin('dp.product', 'p')
            ->where('dp.devis IN (
                SELECT d.id FROM App\Entity\Devis d
                WHERE d.society = :societyId
            )')
            ->groupBy('p.id')
            ->orderBy('totalQuantity', 'ASC')
            ->setMaxResults(1)
            ->setParameter('societyId', ['society' => $society->getId()])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllOrderProductBySociety(Society $society)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT p.name, SUM(dp.quantity) as totalQuantity
            FROM devis_product dp
            RIGHT JOIN product p ON dp.product_id = p.id
            WHERE dp.devis_id IN (
                SELECT d.id FROM Devis d
                WHERE d.society_id = :societyId
            )
            GROUP BY p.id
            ORDER BY totalQuantity ASC;
        ';

        $resultSet = $conn->executeQuery($sql, ['societyId' => $society->getId()]);

        return $resultSet->fetchAllAssociative();

    }

    public function findAllOrderCustomerBySociety(Society $society)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT p.name, SUM(dp.quantity) as totalQuantity
            FROM devis_product dp
            RIGHT JOIN product p ON dp.product_id = p.id
            WHERE dp.devis_id IN (
                SELECT d.id FROM Devis d
                WHERE d.society_id = :societyId
            )
            GROUP BY p.id
            ORDER BY totalQuantity ASC;
        ';

        $resultSet = $conn->executeQuery($sql, ['societyId' => $society->getId()]);

        return $resultSet->fetchAllAssociative();

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
