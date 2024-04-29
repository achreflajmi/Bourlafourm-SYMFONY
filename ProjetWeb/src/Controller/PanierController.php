<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Panier;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


class PanierController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/panier', name: 'app_panier')]
    public function AfficherPanier(EntityManagerInterface $em, UserRepository $userRepository): Response
{ 
    $articlesPanier = $this->getDoctrine()->getManager()->getRepository(Panier::class)->findAll();

    $userId = 1;
    $user = $userRepository->find($userId);

    $totalPanier = 0;
    foreach ($articlesPanier as $article) {
        $totalPanier += $article->getProduit()->getPrixProd() * $article->getQuantitePanier();
    }

    // Mise à jour du totalPanier pour chaque article en fonction de sa quantité
    // Si le totalPanier est une propriété de l'entité Panier, utilisez plutôt cette boucle pour mettre à jour les totaux de chaque panier.

    // foreach ($articlesPanier as $article) {
    //     $articleTotal = $article->getProduit()->getPrixProd() * $article->getQuantitePanier();
    //     $article->setTotalPanier($articleTotal);
    //     $totalPanier += $articleTotal;
    // }

    return $this->render('Front/panier/index.html.twig', [
        'articles' => $articlesPanier,
        'totalPanier' => $totalPanier,
        'user' => $user, 
    ]);
}

    
    

    #[Route('/ajouterPanier/{idProduit}/{userId}', name: 'ajouterPanier')]
public function ajouterAuPanier($idProduit, $userId, EntityManagerInterface $em, UserRepository $userRep, ProduitRepository $pr)
{
    $user = $userRep->find($userId);
    
    $produit = $pr->find($idProduit);
    
    if (!$produit) {
        throw $this->createNotFoundException('Le produit demandé n\'existe pas');
    }
    
    $panier = $em->getRepository(Panier::class)->findOneBy(['produit' => $produit, 'user' => $user]);

    if ($panier) {
        $panier->setQuantitePanier($panier->getQuantitePanier() + 1);
    } else {
        $panier = new Panier();
        $panier->setProduit($produit);
        $panier->setUser($user);
        $panier->setQuantitePanier(1); 
        $panier->setTotalPanier($panier->getProduit()->getPrixProd());

    }
    
    $em->persist($panier);
    $em->flush();
    
    return $this->redirectToRoute('app_panier');
}

    
    
    #[Route('/supprimer_du_panier/{id}', name: 'supprimer_du_panier')]
    public function supprimerDuPanier($id, Request $request, EntityManagerInterface $em): Response
    {
        $panier = $em->getRepository(Panier::class)->find($id);
    
        if (!$panier) {
            $this->addFlash('error', 'Article introuvable dans le panier.');
            return $this->redirectToRoute('app_panier');
        }
    
        $em->remove($panier);
        $em->flush();
    
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['success' => true]);
        }
    
        $this->addFlash('success', 'Article supprimé du panier avec succès.');
        return $this->redirectToRoute('app_panier');
    }
    
public function addToCart($userId, $prodId, UserRepository $userRep, ProduitRepository $pr)
{
    $user = $userRep->find($userId);
    
    if (!$user) {
        throw new \Exception('User not found');
    }

    $produit = $pr->find($prodId);

    if (!$produit) {
        throw new \Exception('Product not found');
    }

    $panier = new Panier();
    $panier->setUser($user);
    $panier->setProduit($produit);

    $this->entityManager->persist($panier);
    $this->entityManager->flush();
}


public function getCartItems($userId)
{
    $userId = 1;

    $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

    if (!$user) {
        throw new \Exception('User not found');
    }

    $cartItems = $user->getPaniers();

    return $cartItems;
}

public function removeFromCart($panierId, PanierRepository $panierRep)
{
    $panier = $panierRep->find($panierId);

    if (!$panier) {
        throw new \Exception('panier item not found');
    }

    $this->entityManager->remove($panier);
    $this->entityManager->flush();
}

public function emptyCart($userId)
{
    $panier = $this->entityManager->getRepository(Panier::class)->findBy([
        'User' => $userId
    ]);

    foreach ($panier as $item) {
        $this->entityManager->remove($item);
    }

    $this->entityManager->flush();
}
}
