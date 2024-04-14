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
use Symfony\Component\HttpFoundation\RedirectResponse;
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
           'p'=>$produits
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
        $produit = new Produit();
        $categorie = new Categorie();
        $form = $this->createForm(ProduitType::class, $produit);
         

        $produit->setNomCategorie(1);
        $form->handleRequest($request);
        $choixCategories = null; // Assign null temporarily
    
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image_prod')->getData();
    
            if ($imageFile instanceof UploadedFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/assets/',
                    $newFilename
                );
                $produit->setImageProd($newFilename);
            }
            $em->persist($produit);
            $em->flush();
    
            return $this->redirectToRoute('display_produit');
        }
    
        $entityManager = $this->getDoctrine()->getManager();
        $produits = $entityManager->getRepository(Produit::class)->findAll();
        $categories = $entityManager->getRepository(Categorie::class)->findAll();
        $categorieChoices = [];
    
        foreach ($categories as $categorie) { 
            $categorieChoices[$categorie->getNomCategorie()] = $categorie->getId();
        }
    
        return $this->renderForm('Back/produit/createProduit.html.twig', [
            'produits' => $produits,
            'f' => $form, // Pass the form object directly
            'categorieChoices' => $categorieChoices,
        ]);
    }
   

    #[Route('/editProduit/{id}', name: 'editProduit')]
    public function modifierProduit(Request $request,$id): Response
    {
            $produit = $this->getDoctrine()->getManager()->getRepository(Produit::class)->find($id);
            $form = $this ->createForm(ProduitType::class,$produit);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $imageFile = $form->get('image_prod')->getData();

                if ($imageFile instanceof UploadedFile) {
                    $newFilename = uniqid().'.'.$imageFile->guessExtension();
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir').'/public/assets/',
                        $newFilename
                    );
                    $produit->setImageProd($newFilename);
                }
    
                    $em->flush();
                return $this->redirectToRoute('display_produit');
            }
            return $this->render('Back/produit/updateProduit.html.twig', ['f' => $form->createView()]);

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
}

