<?php

namespace App\Repository;

use App\Entity\Devis;
use App\Entity\Society;
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

    public function findLastDevisNumberForSociety(\App\Entity\Society $society): ?string
    {

        $lastDevis = $this->createQueryBuilder('d')
            ->where('d.society = :society')
            ->setParameter('society', $society)
            ->orderBy('d.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        if (!$lastDevis) {
            return "wouf";
        }
        return $lastDevis ? $lastDevis->getDevisNumber() : null;
    }

    public function findPendingByUser($society)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.society = :society')
            ->andWhere('d.paymentStatus = :status')
            ->setParameter('society', $society)
            ->setParameter('status', 'PENDING')
            ->getQuery()
            ->getResult();
    }

    public function findAmountDevisPendingForCurrentMonth(Society $society)
    {
        $startDate = new \DateTime('first day of this month');
        $endDate = new \DateTime('last day of this month');

        $result = $this->createQueryBuilder('d')
            ->select('SUM(d.totalDuePrice) as totalDuePrice')
            ->andWhere('d.paymentStatus = :status')
            ->andWhere('d.createdAt BETWEEN :startDate AND :endDate')
            ->andWhere('d.society = :society')
            ->setParameter('status', 'PENDING')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('society', $society)
            ->getQuery()
            ->getOneOrNullResult();
        return $result['totalDuePrice'] ? $result['totalDuePrice'] : 0;
    }

    public function findAmountDevisForCurrentMonth(Society $society)
    {
        $startDate = new \DateTime('first day of this month');
        $endDate = new \DateTime('last day of this month');

        $result = $this->createQueryBuilder('d')
            ->select('SUM(d.totalDuePrice) as totalDuePrice')
            ->andWhere('d.createdAt BETWEEN :startDate AND :endDate')
            ->andWhere('d.society = :society')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('society', $society)
            ->getQuery()
            ->getOneOrNullResult();
        return $result['totalDuePrice'] ? $result['totalDuePrice'] : 0;
    }

    public function findAmountDevisForPreviousMonth(Society $society)
    {
        $startDate = new \DateTime('first day of last month');
        $endDate = new \DateTime('last day of last month');

        $result = $this->createQueryBuilder('d')
            ->select('SUM(d.totalDuePrice) as totalDuePrice')
            ->andWhere('d.createdAt BETWEEN :startDate AND :endDate')
            ->andWhere('d.society = :society')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('society', $society)
            ->getQuery()
            ->getOneOrNullResult();

        return $result['totalDuePrice'] ? $result['totalDuePrice'] : 0;
    }


    public function findAmountInvoicePaid(Society $society)
    {
        return $this->createQueryBuilder('d')
            ->select('SUM(i.totalPrice) as totalReceived')  //TODO TotalDuePrice int
            ->innerJoin('d.invoices', 'i')
            ->where("i.invoiceStatus = 'PAID'")
            ->andWhere('d.society = :society')
            ->setParameter('society', $society)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findInvoicePending(Society $society)
    {
        return $this->createQueryBuilder('d')
            ->select('COUNT(i.id) as totalPending')
            ->innerJoin('d.invoices', 'i')
            ->where("i.invoiceStatus = 'PENDING'")
            ->andWhere('d.society = :society')
            ->setParameter('society', $society)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findDevisPending(Society $society)
    {
        return $this->createQueryBuilder('d')
            ->select('COUNT(d.id) as totalPending')
            ->where("d.paymentStatus = 'PENDING'")
            ->andWhere('d.society = :society')
            ->setParameter('society', $society)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findCustomerWithHighestTotalOrdersAndHisTotalSpending(Society $society)
    {
        return $this->createQueryBuilder('d')
            ->select('c.name', 'COUNT(d.id) as totalOrders', 'SUM(d.totalDuePrice) as totalSpending')
            ->innerJoin('d.customer', 'c')
            ->where('d.society = :society')
            ->groupBy('c.id')
            ->orderBy('totalOrders', 'DESC')
            ->setMaxResults(1)
            ->setParameter('society', $society)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findCustomerWithLowestTotalOrdersAndHisTotalSpending(Society $society)
    {
        return $this->createQueryBuilder('d')
            ->select('c.name', 'COUNT(d.id) as totalOrders', 'SUM(d.totalDuePrice) as totalSpending')
            ->innerJoin('d.customer', 'c')
            ->where('d.society = :society')
            ->groupBy('c.id')
            ->orderBy('totalOrders', 'ASC')
            ->setMaxResults(1)
            ->setParameter('society', $society)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllCustomerWithOrdersAndTotalDuePrice(Society $society)
    {
        return $this->createQueryBuilder('d')
            ->select('c.name', 'COUNT(d.id) as totalOrders', 'SUM(d.totalDuePrice) as totalSpending')
            ->innerJoin('d.customer', 'c')
            ->where('d.society = :society')
            ->groupBy('c.id')
            ->orderBy('totalSpending', 'DESC')
            ->setParameter('society', $society)
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
