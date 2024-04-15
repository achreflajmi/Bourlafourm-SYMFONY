<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Panier;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;


class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function AfficherPanier(EntityManagerInterface $em): Response
    { 
        $articlesPanier = $this->getDoctrine()->getManager()->getRepository(Panier::class)->findAll();
    
        $totalPanier = 0;
        foreach ($articlesPanier as $article) {
            $totalPanier += $article->getProduit()->getPrixProd() * $article->getQuantitePanier();
        }
    
        return $this->render('Front/panier/index.html.twig', [
            'articles' => $articlesPanier,
            'totalPanier' => $totalPanier,
        ]);
    }
    

    #[Route('/ajouterPanier/{idProduit}', name: 'ajouterPanier')] 
    public function ajouterAuPanier($idProduit, EntityManagerInterface $em)
    {
        $produit = $em->getRepository(Produit::class)->find($idProduit);
    
        if (!$produit) {
            throw $this->createNotFoundException('Le produit demandé n\'existe pas');
        }
    
        // Find if the product already exists in the cart
        $panier = $em->getRepository(Panier::class)->findOneBy(['produit' => $produit]);
    
        if ($panier) {
            // Product exists in the cart, increment the quantity
            $panier->setQuantitePanier($panier->getQuantitePanier() + 1);
        } else {
            // Product does not exist, add new
            $panier = new Panier();
            $panier->setProduit($produit);
            $panier->setQuantitePanier(1); // Starting with 1 as it's the first addition
        }
    
        $em->persist($panier);
        $em->flush();
    
        return $this->redirectToRoute('app_panier');
    }
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
    
    
}
