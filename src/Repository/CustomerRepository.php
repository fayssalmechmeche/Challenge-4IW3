<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 *
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function findNewCustomersForCurrentMonth(\App\Entity\User $user)
    {
        $startDate = new \DateTime('first day of this month');
        $endDate = new \DateTime('last day of this month');

        $result = $this->createQueryBuilder('c')
            ->select('COUNT(c.id) as totalCustomers')
            ->andWhere('c.createdAt BETWEEN :startDate AND :endDate')
            ->andWhere('c.user = :user')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
        return $result['totalCustomers'] ? $result['totalCustomers'] : 0;
    }

    public function findNewCustomersForPreviousMonth(\App\Entity\User $user)
    {
        $startDate = new \DateTime('first day of last month');
        $endDate = new \DateTime('last day of last month');

        $result = $this->createQueryBuilder('c')
            ->select('COUNT(c.id) as totalCustomers')
            ->andWhere('c.createdAt BETWEEN :startDate AND :endDate')
            ->andWhere('c.user = :user')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();

        return $result['totalCustomers'] ? $result['totalCustomers'] : 0;
    }

//    /**
//     * @return Customer[] Returns an array of Customer objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Customer
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
