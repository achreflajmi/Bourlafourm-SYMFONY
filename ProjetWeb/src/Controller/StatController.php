<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Coordonnees;

class StatController extends AbstractController
{

    #[Route('/stat', name: 'stat')]
    public function statistiquesIMC(): Response
    {
        // Récupérer les données IMC depuis la base de données
        $coordonneesRepository = $this->getDoctrine()->getRepository(Coordonnees::class);
        $imcData = $coordonneesRepository->findAll();

        // Initialiser les compteurs pour chaque catégorie
        $categoryCounts = [
            'Poids insuffisant' => 0,
            'Poids santé' => 0,
            'Excès de poids' => 0,
            'Obésité' => 0,
        ];

        
        foreach ($imcData as $data) {
            $imc = $data->getImc();
            
            if ($imc < 18.5) {
                $categoryCounts['Poids insuffisant']++;
            } elseif ($imc >= 18.5 && $imc <= 24.9) {
                $categoryCounts['Poids santé']++;
            } elseif ($imc >= 25 && $imc <= 29.9) {
                $categoryCounts['Excès de poids']++;
            } else {
                $categoryCounts['Obésité']++;
            }
        }

        // Afficher les résultats dans le template
        return $this->render('Back/Statistic/stat.html.twig', [
            'imcData' => $imcData,
            'categoryCounts' => $categoryCounts,
            'totalIMC' => count($imcData),
        ]);
    }
}
