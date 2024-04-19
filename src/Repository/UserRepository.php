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
    
    public function updateUser($id, $nom, $email, $image)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'UPDATE App\Entity\User u SET u.nom = :nom, u.email = :email, u.image = :image WHERE u.id = :id'
        );
        $query->setParameter('id', $id);
        $query->setParameter('nom', $nom);
        $query->setParameter('email', $email);
        $query->setParameter('image', $image);
        return $query->getResult();
    }

// Définition de la méthode getUserByEmail, qui prend un argument $email. Cette méthode est utilisée pour trouver un utilisateur dans la base de données en utilisant son adresse e-mail.
public function getUserByEmail($email)   
{
    // Commence la construction d'une requête en utilisant QueryBuilder. 'u' est un alias pour la table utilisateur dans la base de données.
    // La méthode createQueryBuilder est un outil puissant dans Doctrine (le système de gestion de base de données utilisé par Symfony)
    // qui permet de construire des requêtes SQL de manière programmative et sécurisée.
    return $this->createQueryBuilder('u')
        // Ajoute une condition à la requête : sélectionner l'utilisateur dont le champ 'email' correspond à l'adresse e-mail fournie en argument de la méthode.
        // La méthode andWhere est utilisée ici pour ajouter une condition à la requête. ':email' est un paramètre nommé qui sera remplacé par la valeur réelle de l'e-mail.
        ->andWhere('u.email = :email')
        // Associe le paramètre nommé ':email' à la valeur réelle de l'e-mail fournie en argument. Cela empêche les injections SQL en assurant que la valeur est correctement échappée.
        ->setParameter('email', $email)
        // Prépare la requête pour l'exécution. La méthode getQuery convertit la construction de la requête QueryBuilder en une véritable requête Doctrine Query.
        ->getQuery()
        // Exécute la requête et tente de récupérer un seul résultat. getOneOrNullResult essaie de retourner un seul utilisateur qui correspond à la condition.
        // Si aucun utilisateur n'est trouvé, null est retourné à la place d'une exception, évitant ainsi des erreurs pour des requêtes ne renvoyant aucun résultat.
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
