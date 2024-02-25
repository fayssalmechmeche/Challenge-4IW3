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

    public function findMostSoldProductByUser(\App\Entity\User $user)
    {
        return $this->createQueryBuilder('dp')
            ->select('p.name', 'SUM(dp.quantity) as totalQuantity', 'SUM(dp.price) as totalSpending')
            ->innerJoin('dp.product', 'p')
            ->where('dp.devis IN (
                SELECT d.id FROM App\Entity\Devis d
                WHERE d.user = :userId
            )')
            ->groupBy('p.id')
            ->orderBy('totalQuantity', 'DESC')
            ->setMaxResults(1)
            ->setParameter('userId', ['user' => $user->getId()])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findLessSoldProductByUser(\App\Entity\User $user)
    {
        return $this->createQueryBuilder('dp')
            ->select('p.name', 'SUM(dp.quantity) as totalQuantity', 'SUM(dp.price) as totalSpending')
            ->innerJoin('dp.product', 'p')
            ->where('dp.devis IN (
                SELECT d.id FROM App\Entity\Devis d
                WHERE d.user = :userId
            )')
            ->groupBy('p.id')
            ->orderBy('totalQuantity', 'ASC')
            ->setMaxResults(1)
            ->setParameter('userId', ['user' => $user->getId()])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllOrderProductByUser(\App\Entity\User $user)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT p.name, SUM(dp.quantity) as totalQuantity
            FROM devis_product dp
            RIGHT JOIN product p ON dp.product_id = p.id
            WHERE dp.devis_id IN (
                SELECT d.id FROM Devis d
                WHERE d.user_id = :userId
            )
            GROUP BY p.id
            ORDER BY totalQuantity ASC;
        ';

        $resultSet = $conn->executeQuery($sql, ['userId' => $user->getId()]);

        return $resultSet->fetchAllAssociative();

    }

    public function findAllOrderCustomerByUser(\App\Entity\User $user)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT p.name, SUM(dp.quantity) as totalQuantity
            FROM devis_product dp
            RIGHT JOIN product p ON dp.product_id = p.id
            WHERE dp.devis_id IN (
                SELECT d.id FROM Devis d
                WHERE d.user_id = :userId
            )
            GROUP BY p.id
            ORDER BY totalQuantity ASC;
        ';

        $resultSet = $conn->executeQuery($sql, ['userId' => $user->getId()]);

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
