<?php

namespace App\Controller;
use App\Entity\Produit;
use App\Form\ProduitType;

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
    #[Route('/indexFront', name: 'displayFront')]
    public function indexFront(): Response
    {

        $produits= $this->getDoctrine()->getManager()->getRepository(Produit::class)->findAll();
        return $this->render('Back/produit/index.html.twig', [
           'p'=>$produits
        ]);
    }
    #[Route('/admin', name: 'display_produitAdmin')]
    public function indexAdmin(): Response
    {

        return $this->render('Back/indexAdmin.html.twig');
    }
    #[Route('/addProduit', name: 'addProduit')]
    public function addProduit(Request $request): Response
    {
            $produit = new Produit();
            $form = $this ->createForm(ProduitType::class,$produit);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $imageFile = $form->get('image_prod')->getData(); 

                if ($imageFile instanceof UploadedFile) {
                    $newFilename = uniqid().'.'.$imageFile->guessExtension();
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir').'/public/assets/',
                        $newFilename
                    );
                    $produit->setImageProd($newFilename);
                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($produit);
                $em->flush();
                return $this->redirectToRoute('display_produit');
            }
            return $this->render('Back/produit/createProduit.html.twig', ['f' => $form->createView()]);

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

