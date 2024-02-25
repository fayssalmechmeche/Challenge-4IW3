<?php

namespace App\Repository;

use App\Entity\Devis;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Devis>
 *
 * @method Devis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Devis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Devis[]    findAll()
 * @method Devis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Devis::class);
    }

    public function findLastDevisNumberForUser(\App\Entity\User $user): ?string
    {

        $lastDevis = $this->createQueryBuilder('d')
            ->where('d.user = :user')
            ->setParameter('user', $user)
            ->orderBy('d.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        if (!$lastDevis) {
            return "wouf";
        }
        return $lastDevis ? $lastDevis->getDevisNumber() : null;
    }

    public function findPendingByUser($user)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.user = :user')
            ->andWhere('d.paymentStatus = :status')
            ->setParameter('user', $user)
            ->setParameter('status', 'PENDING')
            ->getQuery()
            ->getResult();
    }

    public function findAmountDevisPendingForCurrentMonth(\App\Entity\User $user)
    {
        $startDate = new \DateTime('first day of this month');
        $endDate = new \DateTime('last day of this month');

        $result = $this->createQueryBuilder('d')
            ->select('SUM(d.totalDuePrice) as totalDuePrice')
            ->andWhere('d.paymentStatus = :status')
            ->andWhere('d.createdAt BETWEEN :startDate AND :endDate')
            ->andWhere('d.user = :user')
            ->setParameter('status', 'PENDING')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
        return $result['totalDuePrice'] ? $result['totalDuePrice'] : 0;
        
    }

    public function findAmountDevisForCurrentMonth(\App\Entity\User $user)
    {
        $startDate = new \DateTime('first day of this month');
        $endDate = new \DateTime('last day of this month');

        $result = $this->createQueryBuilder('d')
            ->select('SUM(d.totalDuePrice) as totalDuePrice')
            ->andWhere('d.createdAt BETWEEN :startDate AND :endDate')
            ->andWhere('d.user = :user')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
        return $result['totalDuePrice'] ? $result['totalDuePrice'] : 0;
    }

    public function findAmountDevisForPreviousMonth(\App\Entity\User $user)
    {
        $startDate = new \DateTime('first day of last month');
        $endDate = new \DateTime('last day of last month');

        $result = $this->createQueryBuilder('d')
            ->select('SUM(d.totalDuePrice) as totalDuePrice')
            ->andWhere('d.createdAt BETWEEN :startDate AND :endDate')
            ->andWhere('d.user = :user')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();

        return $result['totalDuePrice'] ? $result['totalDuePrice'] : 0;
    }


    public function findAmountInvoicePaid(\App\Entity\User $user)
    {
        return $this->createQueryBuilder('d')
            ->select('SUM(i.totalPrice) as totalReceived')
            ->innerJoin('d.invoices', 'i')
            ->where("i.paymentStatus = 'PAID'")
            ->andWhere('d.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findInvoicePending(\App\Entity\User $user)
    {
        return $this->createQueryBuilder('d')
            ->select('COUNT(i.id) as totalPending')
            ->innerJoin('d.invoices', 'i')
            ->where("i.paymentStatus = 'PENDING'")
            ->andWhere('d.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findDevisPending(\App\Entity\User $user)
    {
        return $this->createQueryBuilder('d')
            ->select('COUNT(d.id) as totalPending')
            ->where("d.paymentStatus = 'PENDING'")
            ->andWhere('d.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findCustomerWithHighestTotalOrdersAndHisTotalSpending(\App\Entity\User $user)
    {
        return $this->createQueryBuilder('d')
            ->select('c.name', 'COUNT(d.id) as totalOrders', 'SUM(d.totalDuePrice) as totalSpending')
            ->innerJoin('d.customer', 'c')
            ->where('d.user = :user')
            ->groupBy('c.id')
            ->orderBy('totalOrders', 'DESC')
            ->setMaxResults(1)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findCustomerWithLowestTotalOrdersAndHisTotalSpending(\App\Entity\User $user)
    {
        return $this->createQueryBuilder('d')
            ->select('c.name', 'COUNT(d.id) as totalOrders', 'SUM(d.totalDuePrice) as totalSpending')
            ->innerJoin('d.customer', 'c')
            ->where('d.user = :user')
            ->groupBy('c.id')
            ->orderBy('totalOrders', 'ASC')
            ->setMaxResults(1)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllCustomerWithOrdersAndTotalDuePrice(\App\Entity\User $user)
    {
        return $this->createQueryBuilder('d')
            ->select('c.name', 'COUNT(d.id) as totalOrders', 'SUM(d.totalDuePrice) as totalSpending')
            ->innerJoin('d.customer', 'c')
            ->where('d.user = :user')
            ->groupBy('c.id')
            ->orderBy('totalSpending', 'DESC')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Devis[] Returns an array of Devis objects
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

//    public function findOneBySomeField($value): ?Devis
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
