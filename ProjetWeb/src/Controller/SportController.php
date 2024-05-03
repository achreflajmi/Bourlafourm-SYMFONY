<?php

namespace App\Controller;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CoordonneesRepository;
use App\Repository\RegimeRepository;
use App\Repository\ExercicesRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Coordonnees;
use App\Entity\Regime;
use App\Entity\Exercices;
use App\Form\CoordonneesType;
use App\Form\RegimeType;
use App\Form\ExercicesType;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\SmsSender;

use Symfony\Component\HttpFoundation\StreamedResponse;

class SportController extends AbstractController
{
    #[Route('/timer', name: 'timer')]

    public function index(): Response
    {
        return $this->render('timer.html.twig');
    }

    //Export
    public function exportData(Request $request, CoordonneesRepository $coordonneesRepository): StreamedResponse
    {
        $sexe = $request->query->get('sexe');

        $response = new StreamedResponse(function () use ($coordonneesRepository, $sexe) {
            $handle = fopen('php://output', 'wb');
            // Add CSV header
            fputcsv($handle, ['ID', 'Sexe', 'Âge', 'Taille', 'Poids', 'IMC']);

            // Fetch filtered data from repository based on sexe
            $coordonnees = $sexe ? $coordonneesRepository->findBy(['sexe' => $sexe]) : $coordonneesRepository->findAll();

            // Add data rows to CSV
            foreach ($coordonnees as $coord) {
                fputcsv($handle, [
                    $coord->getId(),
                    $coord->getSexe(),
                    $coord->getAge(),
                    $coord->getTaille(),
                    $coord->getPoids(),
                    $coord->getImc(),
                ]);
            }

            fclose($handle);
        });

        // Set response headers
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="coordonnees.csv"');

        return $response;
    }


    //Calculatrice
    #[Route('/calculate-expression', name: 'calculate_expression')]
    public function calculateExpression(Request $request): Response
    {
        // Get the expression submitted by the form
        $expression = $request->request->get('expression');

        // Evaluate the expression
        $result = $this->evaluateExpression($expression);

        // Render the template with the result
        return $this->render('Front/coordonnes/calculator.html.twig', [
            'result' => $result,
        ]);
    }
    private function evaluateExpression($expression)
    {
        try {
            // Use the eval function to evaluate the expression
            $result = eval('return ' . $expression . ';');

            // Return the result
            return $result;
        } catch (\Throwable $th) {
            // Handle any errors that occur during evaluation
            return 'Error: ' . $th->getMessage();
        }
    }




    //fiche
    #[Route('/fiche', name: 'fiche')]
    public function fiche(
        CoordonneesRepository $coordonneesRepository,
        RegimeRepository $regimeRepository,
        ExercicesRepository $exercicesRepository
    ): Response {
        $coordonnees = $coordonneesRepository->findAll();
        $regimes = $regimeRepository->findAll(); // Assurez-vous que cette ligne récupère les régimes depuis la base de données
        $exercices = $exercicesRepository->findAll();

        return $this->render('Back/FichePatient/index.html.twig', [
            'coordonnees' => $coordonnees,
            'regimes' => $regimes, // Assurez-vous que vous passez les régimes au template
            'exercices' => $exercices,
        ]);
    }


    //filtre-sexe
    #[Route('/search-coordinates', name: 'search_coordinates')]
    public function searchCoordinates(
        Request $request,
        CoordonneesRepository $coordonneesRepository,
        RegimeRepository $regimeRepository, // Déclarez les référentiels dans la signature de la méthode
        ExercicesRepository $exercicesRepository
    ): Response {
        $sexe = $request->query->get('sexe');

        if ($sexe) {
            $coordonnees = $coordonneesRepository->findBy(['sexe' => $sexe]);
        } else {
            $coordonnees = $coordonneesRepository->findAll();
        }

        // Récupérez les régimes depuis le référentiel des régimes
        $regimes = $regimeRepository->findAll();

        return $this->render('Back/FichePatient/filterCoord.html.twig', [
            'coordonnees' => $coordonnees,
            'regimes' => $regimes, // Passez les régimes au template
            'exercices' => $exercicesRepository->findAll(), // Assurez-vous de passer les exercices également si nécessaire
        ]);
    }





    //pack
    #[Route('/pack', name: 'pack')]
    public function pack(): Response
    {
        return $this->render('Front/pack/index.html.twig');
    }
    //cooordonnnes 

    #[Route('/createCoordonnees', name: 'create_coordonnees')]
    public function createCoordonnees(Request $request, EntityManagerInterface $entityManager, CoordonneesRepository $coordonneesRepository): Response
    {
        $coordonnees = new Coordonnees();
        $form = $this->createForm(CoordonneesType::class, $coordonnees);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Calcul de l'IMC
            $imc = $coordonnees->calculateImc();
            $coordonnees->setImc($imc);

            $entityManager->persist($coordonnees);
            $entityManager->flush();
        }

        $Coordonnees = $coordonneesRepository->findAll();
        return $this->render('Front/coordonnes/index.html.twig', [
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


        return $this->render('Front/coordonnes/edit.html.twig', [
            'formAdd' => $form->createView(),
        ]);
    }







    #[Route('/Coordonnees/delete/{id}', name: 'delete_Coordonnees')]
    public function deletecoordonnes($id, EntityManagerInterface $entityManager, CoordonneesRepository $coordonneesRepository, CoordonneesRepository $RR): Response
    {
        $coordonnees = $coordonneesRepository->find($id);
        $form = $this->createForm(CoordonneesType::class, $coordonnees);
        if (!$coordonnees) {
            throw $this->createNotFoundException('coordonnees non trouvé avec l\'identifiant : ' . $id);
        }

        $entityManager->remove($coordonnees);
        $entityManager->flush();

        $Coordonnees = $RR->findAll();

        return $this->redirectToRoute('create_coordonnees');
        return $this->render('Front/coordonnes/index.html.twig', [
            'form' => $form->createView(),
            'Coordonnees' => $Coordonnees,
        ]);
    }



    // regine 

    #[Route('/createRegime', name: 'create_Regime')]
    public function createRegime(Request $request, RegimeRepository $RR): Response
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
        return $this->render('Front/regime/index.html.twig', [
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


        return $this->render('Front/regime/edit.html.twig', [
            'formAdd' => $form->createView(),
        ]);
    }




    #[Route('/Regime/delete/{id}', name: 'delete_regime')]
    public function deleteRegime($id, EntityManagerInterface $entityManager, RegimeRepository $regimeRepository, RegimeRepository $RR): Response
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

        return $this->render('Front/regime/index.html.twig', [
            'form' => $form->createView(),
            'Regime' => $Regime,
        ]);
    }

    //Exercices 


    #[Route('/createEx', name: 'create_Ex')]
    public function createEx(Request $request, ExercicesRepository $RR): Response
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

        return $this->render('Front/exercices/index.html.twig', [
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


        return $this->render('Front/exercices/edit.html.twig', [
            'formAdd' => $form->createView(),
        ]);
    }


    #[Route('/exercices/delete/{id}', name: 'delete_exercices')]
    public function deleteexo($id, EntityManagerInterface $entityManager, ExercicesRepository $regimeRepository, ExercicesRepository $RR): Response
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

        return $this->render('Front/exercices/index.html.twig', [
            'form' => $form->createView(),
            'Exercices' => $Exercices,
        ]);
    }
}
