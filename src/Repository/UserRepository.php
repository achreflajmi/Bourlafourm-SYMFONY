<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
  
    public function getUserById($id)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    public function updateUser($id, $nom, $prenom, $email, $motpass, $image)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'UPDATE App\Entity\User u SET u.nom = :nom, u.prenom =:prenom, u.email = :email, u.motpass = :motpass, u.image = :image WHERE u.id = :id'
        );
        $query->setParameter('id', $id);
        $query->setParameter('nom', $nom);
        $query->setParameter('prenom', $prenom);
        $query->setParameter('email', $email);
        $query->setParameter('motpass', $motpass);
        $query->setParameter('image', $image);
        return $query->getResult();
    }

public function getUserByEmail($email)   
{
    return $this->createQueryBuilder('u')
        ->andWhere('u.email = :email')

        ->setParameter('email', $email)

        ->getQuery()
        
        ->getOneOrNullResult(); 
}


    public function getUserByResetCode($resetCode)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.resetCode = :resetCode')
            ->setParameter('resetCode', $resetCode)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
