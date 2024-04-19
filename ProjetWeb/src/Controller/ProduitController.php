<?php

namespace App\Controller;
use App\Entity\Produit;
use App\Entity\Categorie;
use App\Form\ProduitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProduitController extends AbstractController
{
    #[Route('/AfficherProduit', name: 'display_produit')]
    public function index(): Response
    {

        $produits= $this->getDoctrine()->getManager()->getRepository(Produit::class)->findAll();
        return $this->render('Back/produit/index.html.twig', [
           'p'=>$produits
        ]);
    }
    #[Route('/produits', name: 'displayFront')]
    public function indexFront(): Response
    {
        $produits= $this->getDoctrine()->getManager()->getRepository(Produit::class)->findAll();
        
        return $this->render('Front/produit/index.html.twig', [
           'p' => $produits 
        ]);
    }
    #[Route('/admin', name: 'display_produitAdmin')]
    public function indexAdmin(): Response
    {

        return $this->render('Back/indexAdmin.html.twig');
    }
    
    #[Route('/addProduit', name: 'addProduit')]
    public function addProduit(Request $request, EntityManagerInterface $em): Response
    {
        // Fetch category entities instead of just names
        $entityManager = $this->getDoctrine()->getManager();
        $categories = $entityManager->getRepository(Categorie::class)->findAll();
        $categoryChoices = [];
        foreach ($categories as $categorie) {
            $categoryChoices[$categorie->getNomCategorie()] = $categorie;
        }
    
        // Create the form with category choices
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit, [
            'categoryChoices' => $categoryChoices,
        ]);
    
        // Handle form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $imageFile = $form->get('image_prod')->getData();
            if ($imageFile instanceof UploadedFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/assets/',
                    $newFilename
                );
                $produit->setImageProd($newFilename);
            }
    
            // Persist and flush the entity
            $em->persist($produit);
            $em->flush();
    
            // Redirect to the desired route after successful submission
            return $this->redirectToRoute('display_produit');
        }
    
        // Render the form
        return $this->render('Back/produit/createProduit.html.twig', [
            'f' => $form->createView(),
        ]);
    }
    
    
   

    #[Route('/editProduit/{id}', name: 'editProduit')]
    public function modifierProduit(Request $request, $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        // Fetch category entities
        $categories = $entityManager->getRepository(Categorie::class)->findAll();
        $categoryChoices = [];
        foreach ($categories as $categorie) {
            $categoryChoices[$categorie->getNomCategorie()] = $categorie;
        }
        
        // Find the product entity by its ID
        $produit = $entityManager->getRepository(Produit::class)->find($id);
        
        // Create the form with category choices and pre-populate with existing data
        $form = $this->createForm(ProduitType::class, $produit, [
            'categoryChoices' => $categoryChoices,
        ]);
    
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $imageFile = $form->get('image_prod')->getData();
            if ($imageFile instanceof UploadedFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('kernel.project_dir').'/public/assets/',
                    $newFilename
                );
                $produit->setImageProd($newFilename);
            }
    
            // Flush the changes
            $entityManager->flush();
    
            // Redirect to the desired route after successful submission
            return $this->redirectToRoute('display_produit');
        }
    
        return $this->render('Back/produit/updateProduit.html.twig', [
            'f' => $form->createView(),
        ]);
    }
    
    #[Route('/deleteProduit/{id}', name: 'suppProduit')]
    public function supprimerProduit(int $id, SessionInterface $session): Response
    {
        // Get the EntityManager
        $entityManager = $this->getDoctrine()->getManager();

        // Find the product entity by its ID
        $produit = $entityManager->getRepository(Produit::class)->find($id);

        // If no product found, create a flash message and redirect
        if (!$produit) {
            $session->getFlashBag()->add('error', 'Product not found.');
            return $this->redirectToRoute('display_produit');
        }

        // Remove the product entity
        $entityManager->remove($produit);
        $entityManager->flush();

        // Create a success flash message and redirect
        $session->getFlashBag()->add('success', 'Product deleted successfully.');
        return $this->redirectToRoute('display_produit');
    }
    #[Route('/searchProduit', name: 'search_produit')]
    public function searchProduit(Request $request, EntityManagerInterface $em): Response
    {
        $query = $request->request->get('query');
    
        $results = $em->getRepository(Produit::class)->search($query);
    
        // Render only the search results template without the full layout
        return $this->render('Front/produit/search_results.html.twig', [
            'results' => $results,
        ]);
    }
    #[Route('/searchProduitB', name: 'search_produitB')]
    public function searchProduitB(Request $request, EntityManagerInterface $em): Response
    {
        $query = $request->request->get('query');
    
        $results = $em->getRepository(Produit::class)->search($query);
    
        // Render only the search results template without the full layout
        return $this->render('Back/produit/search_results.html.twig', [
            'results' => $results,
        ]);
    }
    
    
}

