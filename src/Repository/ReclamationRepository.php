<?php

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reclamation>
 *
 * @method Reclamation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reclamation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reclamation[]    findAll()
 * @method Reclamation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReclamationRepository extends ServiceEntityRepository
{

  public function findReclamationsByEtat($etat)
    {
        return $this->createQueryBuilder('r')
            ->where('r.Etat = :etat')
            ->setParameter('etat', $etat)
            ->getQuery()
            ->getResult();
    }




    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }
    public function findByObjet(string $searchTerm): array
{
    $queryBuilder = $this->createQueryBuilder('o');

    if (!empty($searchTerm)) {
        $queryBuilder->andWhere('o.obj LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$searchTerm.'%');
    }

    return $queryBuilder->getQuery()->getResult();
}

}
