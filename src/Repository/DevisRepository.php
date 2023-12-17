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
