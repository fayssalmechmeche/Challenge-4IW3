<?php

namespace App\Repository;

use App\Entity\DevisFormula;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DevisFormula|null find($id, $lockMode = null, $lockVersion = null)
 * @method DevisFormula|null findOneBy(array $criteria, array $orderBy = null)
 * @method DevisFormula[]    findAll()
 * @method DevisFormula[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevisFormulaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DevisFormula::class);
    }


}
