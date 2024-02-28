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

    public function findLastinvoiceNumberForUser(\App\Entity\Society $society): ?string
    {

        $lastInvoice = $this->createQueryBuilder('d')
            ->where('d.society = :society')
            ->setParameter('society', $society)
            ->orderBy('d.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        if (!$lastInvoice) {
            return "wouf";
        }
        return $lastInvoice ? $lastInvoice->getInvoiceNumber() : null;
    }

    // Vous pouvez ajouter des méthodes personnalisées ici si nécessaire
}
