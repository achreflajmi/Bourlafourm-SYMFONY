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
        $articlesPanier= $this->getDoctrine()->getManager()->getRepository(Produit::class)->findAll();
    
        return $this->render('Front/panier/index.html.twig', [
            'articles' => $articlesPanier,
        ]);
    }

    #[Route('/ajouterPanier/{idProduit}', name: 'ajouterPanier')] 
    public function ajouterAuPanier($idProduit, EntityManagerInterface $em)
    {
        // Find the product by its ID
        $produit = $em->getRepository(Produit::class)->find($idProduit);
    
        // Check if the product exists
        if (!$produit) {
            throw $this->createNotFoundException('Le produit demandé n\'existe pas');
        }
    
        // Create a new Panier instance
        $panier = new Panier();
        // Set the product for the panier
        $panier->setProduit($produit);
        // Set the quantity of the product in the panier
        $panier->setQuantitePanier(1);
    
        // Persist and flush the panier entity
        $em->persist($panier);
        $em->flush();
    
        // Redirect to the panier page
        return $this->redirectToRoute('app_panier'); 
    }
    
    
}
