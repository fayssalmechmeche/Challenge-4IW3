<?php

namespace App\Repository;

use App\Entity\Invoice;
use App\Entity\Society;
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

    public function findLastInvoiceAmountForSociety(Society $society): ?string
    {

        $lastInvoice = $this->createQueryBuilder('i')
            ->where('i.society = :society')
            ->andWhere('i.invoiceStatus = :status')
            ->setParameter('status', 'PAID')
            ->setParameter('society', $society)
            ->orderBy('i.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        return $lastInvoice ? $lastInvoice->getTotalPrice() : null;
    }

    public function findAllInvoiceAmountForSociety(Society $society): ?array
    {

        return $this->createQueryBuilder('i')
            ->where('i.society = :society')
            ->andWhere('i.invoiceStatus = :status')
            ->setParameter('status', 'PAID')
            ->setParameter('society', $society)
            ->orderBy('i.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // Vous pouvez ajouter des méthodes personnalisées ici si nécessaire
}
