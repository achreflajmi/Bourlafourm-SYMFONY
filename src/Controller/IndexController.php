<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/indexBack', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('Back/indexAdmin.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
    #[Route('/indexFront', name: 'app_index')]
    public function indexFront(): Response
    {
        return $this->render('Front/indexFront.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
}
