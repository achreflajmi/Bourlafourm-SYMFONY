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
    
        return $this->render('Front/panier/index.html.twig', [
            'articles' => $articlesPanier,
            'totalPanier' => $totalPanier,
            'user' => $user, 
        ]);
    }
    

    #[Route('/ajouterPanier/{idProduit}/{userId}', name: 'ajouterPanier')]
public function ajouterAuPanier($idProduit, $userId, EntityManagerInterface $em, UserRepository $userRep, ProduitRepository $pr)
{
    // Find the user by ID
    $user = $userRep->find($userId);
    
    // Find the product by ID
    $produit = $pr->find($idProduit);
    
    // Check if the product exists
    if (!$produit) {
        throw $this->createNotFoundException('Le produit demandé n\'existe pas');
    }
    
    // Find if the product already exists in the cart
    $panier = $em->getRepository(Panier::class)->findOneBy(['produit' => $produit, 'user' => $user]);

    // If the product exists in the cart, increment the quantity
    if ($panier) {
        $panier->setQuantitePanier($panier->getQuantitePanier() + 1);
    } else {
        // If the product does not exist, create a new Panier entry
        $panier = new Panier();
        $panier->setProduit($produit);
        $panier->setUser($user);
        $panier->setQuantitePanier(1); // Set quantity to 1
    }
    
    // Persist the changes to the database
    $em->persist($panier);
    $em->flush();
    
    // Redirect the user to the cart page
    return $this->redirectToRoute('app_panier');
}


        // public function addToPanier(Request $request, $idProduit, ProduitRepository $produitRepository): Response
        // {
        //     // Manually fetch a user by ID
        //     $userId = 1; // Replace with the ID of the user you want to associate the cart with
        //     $user = $this->getDoctrine()->getRepository(User::class)->find($userId);
    
        //     // Find the product by its ID
        //     $produit = $produitRepository->find($idProduit);
    
        //     if (!$produit) {
        //         throw $this->createNotFoundException('Le produit demandé n\'existe pas');
        //     }
    
        //     // Check if the user already has the product in their cart
        //     if ($user->getPaniers()->contains($produit)) {
        //         // If the product is already in the cart, handle accordingly (e.g., increase quantity, display message)
        //         // You can add your logic here
        //         $this->addFlash('info', 'Cet article est déjà dans votre panier.');
        //     } else {
        //         // If the product is not in the cart, add it
        //         $user->getPaniers()->add($produit);
        //         // Persist changes to the database
        //         $entityManager = $this->getDoctrine()->getManager();
        //         $entityManager->flush();
        //         // Add flash message
        //         $this->addFlash('success', 'Article ajouté au panier.');
        //     }
    
        //     return $this->redirectToRoute('app_panier');
        // }
    
    
    #[Route('/supprimer_du_panier/{id}', name: 'supprimer_du_panier')]
public function supprimerDuPanier($id, EntityManagerInterface $em): Response
{
    $panier = $em->getRepository(Panier::class)->find($id);

    if (!$panier) {
        $this->addFlash('error', 'Article introuvable dans le panier.');
        return $this->redirectToRoute('app_panier');
    }

    $em->remove($panier);
    $em->flush();

    $this->addFlash('success', 'Article supprimé du panier avec succès.');
    return $this->redirectToRoute('app_panier');
}
    
public function addToCart($userId, $prodId, UserRepository $userRep, ProduitRepository $pr)
{
    // Fetch the user object using the provided user ID
    $user = $userRep->find($userId);
    
    if (!$user) {
        throw new \Exception('User not found');
    }

    // Fetch the product object using the provided product ID
    $produit = $pr->find($prodId);

    if (!$produit) {
        throw new \Exception('Product not found');
    }

    // Create a new Panier object
    $panier = new Panier();
    // Set the user and product properties
    $panier->setUser($user);
    $panier->setProduit($produit);
    // Set any additional properties if needed
    // $panier->setDateAjout(new \DateTime());

    // Persist the Panier object
    $this->entityManager->persist($panier);
    $this->entityManager->flush();
}


public function getCartItems($userId)
{
    $userId = 1;

    $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

    // Check if the user exists
    if (!$user) {
        throw new \Exception('User not found');
    }

    // Retrieve the cart items associated with the user
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
