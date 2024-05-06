<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class CategorieController extends AbstractController
{
    #[Route('/AfficherCategorie', name: 'display_categorie')]
    public function index(): Response
    {

        $categories= $this->getDoctrine()->getManager()->getRepository(Categorie::class)->findAll();
        return $this->render('Back/categorie/index.html.twig', [
           'c'=>$categories
        ]);
    }
    #[Route('/admin', name: 'display_categorieAdmin')]
    public function indexAdmin(): Response
    {

        return $this->render('Back/indexAdmin.html.twig');
    }


    #[Route('/addCategorie', name: 'addCategorie')]
    public function addCategorie(Request $request, EntityManagerInterface $em): Response
    {
        $Categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $Categorie);
                 $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em->persist($Categorie);
            $em->flush();
    
            return $this->redirectToRoute('display_categorie');
        }
    
        $entityManager = $this->getDoctrine()->getManager();
        $categories = $entityManager->getRepository(Categorie::class)->findAll();
    
      
    
        return $this->renderForm('Back/categorie/createCategorie.html.twig', [
            'categories' => $categories,
            'c' => $form, 
        ]);
    }
   

    #[Route('/editCategorie/{id}', name: 'editCategorie')]
    public function modifierCategorie(Request $request,$id): Response
    {
            $categorie = $this->getDoctrine()->getManager()->getRepository(Categorie::class)->find($id);
            $form = $this ->createForm(CategorieType::class,$categorie);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $em = $this->getDoctrine()->getManager();
              
    
                    $em->flush();
                return $this->redirectToRoute('display_categorie');
            }
            return $this->render('Back/categorie/updateCategorie.html.twig', ['c' => $form->createView()]);

    }

    #[Route('/deleteCategorie/{id}', name: 'suppCategorie')]
    public function supprimerCategorie(int $id, SessionInterface $session): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $categorie = $entityManager->getRepository(Categorie::class)->find($id);

        if (!$categorie) {
            $session->getFlashBag()->add('error', 'Categorie not found.');
            return $this->redirectToRoute('display_categorie');
        }

        $entityManager->remove($categorie);
        $entityManager->flush();

        // Create a success flash message and redirect
        $session->getFlashBag()->add('success', 'categorie deleted successfully.');
        return $this->redirectToRoute('display_categorie');
    }
}
