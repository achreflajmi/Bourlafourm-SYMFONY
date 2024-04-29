<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }
    public function search($query)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.nom_prod LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->getQuery()
            ->getResult();
    }
    public function findAllSorted(): array
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->orderBy('p.prix_prod', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }
    public function findAllSorted1(): array
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->orderBy('p.prix_prod', 'DESC');

        return $queryBuilder->getQuery()->getResult();
    }

    public function countByNomCategorie($nomCategorie)
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->leftJoin('p.categorie', 'c')
            ->where('c.nom_categorie = :nomCategorie')
            ->setParameter('nomCategorie', $nomCategorie)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function getAllCategoryNames()
    {
        return $this->createQueryBuilder('p')
            ->select('DISTINCT c.nom_categorie')
            ->leftJoin('p.categorie', 'c')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Produit[] Returns an array of Produit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Produit
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function calculateAverageRating($productId)
    {
        $query = $this->createQueryBuilder('p')
            ->select('AVG(p.rating) AS averageRating')
            ->where('p.id = :productId')
            ->setParameter('productId', $productId)
            ->getQuery();

        $result = $query->getOneOrNullResult();

        // If there are no ratings, return null
        if ($result === null) {
            return null;
        }

        // Return the average rating
        return $result['averageRating'];
    }
}
