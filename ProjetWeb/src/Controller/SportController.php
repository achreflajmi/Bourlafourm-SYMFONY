<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CoordonneesRepository;
use App\Repository\RegimeRepository;
use App\Repository\ExercicesRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Coordonnees ;
use App\Entity\Regime ;
use App\Entity\Exercices ;
use App\Form\CoordonneesType;
use App\Form\RegimeType;
use App\Form\ExercicesType;
use Doctrine\ORM\EntityManagerInterface;


class SportController extends AbstractController
{
    //cooordonnnes 

    #[Route('/createCoordonnees', name: 'create_coordonnees')]
    public function createCoordonnees(Request $request,CoordonneesRepository $RR): Response
    {
        $coordonnees = new Coordonnees();
        $form = $this->createForm(CoordonneesType::class, $coordonnees);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($coordonnees);
            $entityManager->flush();

            return $this->redirectToRoute('create_coordonnees');
        }
        $Coordonnees = $RR->findAll();
        return $this->render('coordonnes/index.html.twig', [
            'form' => $form->createView(),
            'Coordonnees' => $Coordonnees,
        ]);
    }


   #[Route('/Coordonnees/edit/{id}', name: 'editcorrrrr')]
    public function editreclamationForm($id, EntityManagerInterface $em, CoordonneesRepository $p, Request $req): Response
    {
        
        $coordonnees = $p->find($id);

        $form = $this->createForm(CoordonneesType::class, $coordonnees);

 
        $form->handleRequest($req);

      
        if ($form->isSubmitted() && $form->isValid()) {
       
            $em->persist($coordonnees);
            $em->flush();

            return $this->redirectToRoute('create_coordonnees');
        }

        
        return $this->render('coordonnes/edit.html.twig', [
            'formAdd' => $form->createView(), 
        ]);
    }







    #[Route('/Coordonnees/delete/{id}', name: 'delete_Coordonnees')]
    public function deletecoordonnes($id, EntityManagerInterface $entityManager, CoordonneesRepository $coordonneesRepository,CoordonneesRepository $RR): Response
    {
        $coordonnees =$coordonneesRepository->find($id);
        $form = $this->createForm(CoordonneesType::class, $coordonnees);
        if (!$coordonnees) {
            throw $this->createNotFoundException('coordonnees non trouvé avec l\'identifiant : ' . $id);
        }

        $entityManager->remove($coordonnees);
        $entityManager->flush();

        $Coordonnees = $RR->findAll();

        return $this->redirectToRoute('create_coordonnees');
        return $this->render('coordonnes/index.html.twig', [
            'form' => $form->createView(),
            'Coordonnees' => $Coordonnees,
        ]);
        
        
    }

   

// regine 

    #[Route('/createRegime', name: 'create_Regime')]
    public function createRegime(Request $request ,RegimeRepository $RR): Response
    {
        $regime = new Regime();
        $form = $this->createForm(RegimeType::class, $regime);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($regime);
            $entityManager->flush();


            return $this->redirectToRoute('create_Regime');
        }

        $Regime = $RR->findAll();
        return $this->render('regime/index.html.twig', [
            'form' => $form->createView(),
            'Regime' => $Regime,
        ]);
    }




    #[Route('/Regime/edit/{id}', name: 'edit')]
    public function editRegime($id, EntityManagerInterface $em, RegimeRepository $p, Request $req): Response
    {
        
        $regime = $p->find($id);

        $form = $this->createForm(RegimeType::class, $regime);

 
        $form->handleRequest($req);

      
        if ($form->isSubmitted() && $form->isValid()) {
       
            $em->persist($regime);
            $em->flush();

            return $this->redirectToRoute('create_Regime');
        }

        
        return $this->render('regime/edit.html.twig', [
            'formAdd' => $form->createView(), 
        ]);
    }




    #[Route('/Regime/delete/{id}', name: 'delete_regime')]
    public function deleteRegime($id, EntityManagerInterface $entityManager, RegimeRepository $regimeRepository,RegimeRepository $RR): Response
    {
        $regime = $regimeRepository->find($id);
        $form = $this->createForm(RegimeType::class, $regime);
        if (!$regime) {
            throw $this->createNotFoundException('Régime non trouvé avec l\'identifiant : ' . $id);
        }

        $entityManager->remove($regime);
        $entityManager->flush();

        $Regime = $RR->findAll();

        return $this->redirectToRoute('create_Regime');

        return $this->render('regime/index.html.twig', [
            'form' => $form->createView(),
            'Regime' => $Regime,
        ]);
        
        
    }

//Exercices 


    #[Route('/createEx', name: 'create_Ex')]
    public function createEx(Request $request ,ExercicesRepository $RR): Response
    {
        $exercices = new Exercices();
        $form = $this->createForm(ExercicesType::class, $exercices);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
         
    
            $file = $form->get('image')->getData();
            if ($file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                }
                $exercices->setImage($fileName);
            }
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($exercices);
            $entityManager->flush();
    
            return $this->redirectToRoute('create_Ex');
        }
    
        $Exercices = $RR->findAll();

        return $this->render('exercices/index.html.twig', [
            'form' => $form->createView(),
            'Exercices' => $Exercices,
        ]);
    }
    




    #[Route('/Exercices/edit/{id}', name: 'editEXO')]
    public function editEXO($id, EntityManagerInterface $em, ExercicesRepository $p, Request $req): Response
    {
        
        $exercices = $p->find($id);

        $form = $this->createForm(ExercicesType::class, $exercices);

 
        $form->handleRequest($req);

      
        if ($form->isSubmitted() && $form->isValid()) {
       


            $file = $form->get('image')->getData();
            if ($file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                }
                $exercices->setImage($fileName);
            }


            $em->persist($exercices);
            $em->flush();

            return $this->redirectToRoute('create_Ex');
        }

        
        return $this->render('exercices/edit.html.twig', [
            'formAdd' => $form->createView(), 
        ]);
    }


    #[Route('/exercices/delete/{id}', name: 'delete_exercices')]
    public function deleteexo($id, EntityManagerInterface $entityManager, ExercicesRepository $regimeRepository,ExercicesRepository $RR): Response
    {
        $exercices = $regimeRepository->find($id);
        $form = $this->createForm(ExercicesType::class, $exercices);
        if (!$exercices) {
            throw $this->createNotFoundException('Régime non trouvé avec l\'identifiant : ' . $id);
        }

        $entityManager->remove($exercices);
        $entityManager->flush();

        $Exercices = $RR->findAll();
          return $this->redirectToRoute('create_Ex');

        return $this->render('exercices/index.html.twig', [
            'form' => $form->createView(),
            'Exercices' => $Exercices,
        ]);
        
        
    }

    
}