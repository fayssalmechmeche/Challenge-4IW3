<?php

namespace App\Repository;

use App\Entity\Invoice;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    public function findLastinvoiceNumberForUser(\App\Entity\User $user): ?string
    {

        $lastInvoice = $this->createQueryBuilder('d')
            ->where('d.user = :user')
            ->setParameter('user', $user)
            ->orderBy('d.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        if (!$lastInvoice) {
            return "wouf";
        }
        return $lastInvoice ? $lastInvoice->getInvoiceNumber() : null;
    }

    public function findLastInvoiceAmountForUser(\App\Entity\User $user): ?string
    {

        $lastInvoice = $this->createQueryBuilder('d')
            ->where('d.user = :user')
            ->andWhere('d.paymentStatus = :status')
            ->setParameter('status', 'PAID')
            ->setParameter('user', $user)
            ->orderBy('d.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        return $lastInvoice ? $lastInvoice->getTotalDuePrice() : null;
    }

    // Vous pouvez ajouter des méthodes personnalisées ici si nécessaire
}
