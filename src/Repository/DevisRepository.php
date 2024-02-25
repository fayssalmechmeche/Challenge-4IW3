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
