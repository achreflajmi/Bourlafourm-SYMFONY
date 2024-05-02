<?php

namespace App\Controller;
use App\Entity\Produit;
use App\Form\RatingType;
use App\Entity\Ratings;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use App\Entity\Categorie;
use App\Form\ProduitType;
use App\Repository\UserRepository;
use App\Repository\RatingsRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Knp\Component\Pager\PaginatorInterface;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

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
    public function indexFront(Request $request, PaginatorInterface $paginator, UserRepository $userRepository): Response
    {
        $userId = 1;
        $user = $userRepository->find($userId);
        $produits= $this->getDoctrine()->getManager()->getRepository(Produit::class)->findAll();
        // Fetch the query builder for products
        $queryBuilder = $this->getDoctrine()->getManager()->getRepository(Produit::class)->createQueryBuilder('p');
        
        // Paginate the query builder
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1), // Get the current page number from the request
            3 // Items per page
        );
    
        // Iterate through pagination to calculate average rating for each product
        foreach ($pagination as $product) {
            $averageRating = $this->calculateAverageRating($product);
            $product->setRating($averageRating);
        }
        
        return $this->render('Front/produit/index.html.twig', [
            'p' => $produits ,
           'pagination' => $pagination,
           'user' => $user,
        ]);
    }
    
    private function calculateAverageRating(Produit $product): float
{
    $ratings = $product->getRatings();
    $totalRating = 0;
    $totalRatings = count($ratings);
    
    if ($totalRatings === 0) {
        return 0; // No ratings available
    }

    foreach ($ratings as $rating) {
        $totalRating += $rating->getRating();
    }

    return $totalRating / $totalRatings;
}
    #[Route('/admin', name: 'display_produitAdmin')]
    public function indexAdmin(): Response
    {

        return $this->render('Back/indexAdmin.html.twig');
    }
    
    #[Route('/addProduit', name: 'addProduit')]
    public function addProduit(Request $request, EntityManagerInterface $em): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $categories = $entityManager->getRepository(Categorie::class)->findAll();
        $categoryChoices = [];
        foreach ($categories as $categorie) {
            $categoryChoices[$categorie->getNomCategorie()] = $categorie;
        }
    
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit, [
            'categoryChoices' => $categoryChoices,
        ]);
    
        $form->handleRequest($request);
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
    
        return $this->render('Back/produit/createProduit.html.twig', [
            'f' => $form->createView(),
        ]);
    }
    
    
   

    #[Route('/editProduit/{id}', name: 'editProduit')]
    public function modifierProduit(Request $request, $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $categories = $entityManager->getRepository(Categorie::class)->findAll();
        $categoryChoices = [];
        foreach ($categories as $categorie) {
            $categoryChoices[$categorie->getNomCategorie()] = $categorie;
        }
        
        $produit = $entityManager->getRepository(Produit::class)->find($id);
        
        $form = $this->createForm(ProduitType::class, $produit, [
            'categoryChoices' => $categoryChoices,
        ]);
    
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image_prod')->getData();
            if ($imageFile instanceof UploadedFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('kernel.project_dir').'/public/assets/',
                    $newFilename
                );
                $produit->setImageProd($newFilename);
            }
    
            $entityManager->flush();
    
            return $this->redirectToRoute('display_produit');
        }
    
        return $this->render('Back/produit/updateProduit.html.twig', [
            'f' => $form->createView(),
        ]);
    }
    
    #[Route('/deleteProduit/{id}', name: 'suppProduit')]
    public function supprimerProduit(int $id, SessionInterface $session): Response
    {
    
        $entityManager = $this->getDoctrine()->getManager();

        $produit = $entityManager->getRepository(Produit::class)->find($id);

        if (!$produit) {
            $session->getFlashBag()->add('error', 'Product not found.');
            return $this->redirectToRoute('display_produit');
        }

        $entityManager->remove($produit);
        $entityManager->flush();

        $session->getFlashBag()->add('success', 'Product deleted successfully.');
        return $this->redirectToRoute('display_produit');
    }
    #[Route('/searchProduit', name: 'search_produit')]
    public function searchProduit(Request $request, EntityManagerInterface $em,  PaginatorInterface $paginator, UserRepository $userRepository): Response
    {
        $userId = 1;
        $user = $userRepository->find($userId);
        $query = $request->request->get('query');
    
        $results = $em->getRepository(Produit::class)->search($query);
    
        return $this->render('Front/produit/search_results.html.twig', [
            'results' => $results,
            'user' => $user,
        ]);
    }
    #[Route('/searchProduitB', name: 'search_produitB')]
    public function searchProduitB(Request $request, EntityManagerInterface $em): Response
    {
        $query = $request->request->get('query');
    
        $results = $em->getRepository(Produit::class)->search($query);
    
        return $this->render('Back/produit/search_results.html.twig', [
            'results' => $results,
        ]);
    }
    #[Route('/AfficherProduit/tricroi', name: 'tri', methods: ['GET', 'POST'])]
    public function triCroissant(\App\Repository\ProduitRepository $pr): Response
    {
        $produit = $pr->findAllSorted();

        return $this->render('Back/produit/index.html.twig', [
            'p' => $produit,
        ]);
    }

    #[Route('/AfficherProduit/tridesc', name: 'trid', methods: ['GET', 'POST'])]
    public function triDescroissant(\App\Repository\ProduitRepository $pr): Response
    {
        $produit = $pr->findAllSorted1();

        return $this->render('Back/produit/index.html.twig', [
            'p' => $produit,
        ]);
    }

    #[Route('/AfficherProduit/stat', name: 'stat', methods: ['POST', 'GET'])]
public function statisticsByCategory(\App\Repository\ProduitRepository $repo, Request $request): Response
{
    $categories = $repo->getAllCategoryNames();

    $categoryStatistics = [];

    $total = 0;

    foreach ($categories as $category) {
        $count = $repo->countByNomCategorie($category['nom_categorie']);
        $total += $count;
        $categoryStatistics[$category['nom_categorie']] = $count;
    }

    if ($total != 0) {
        foreach ($categoryStatistics as $category => &$count) {
            $percentage = round(($count / $total) * 100);
            $count = [
                'count' => $count,
                'percentage' => $percentage,
            ];
        }
    }

    return $this->render('/Back/produit/stat.html.twig', [
        'categoryStatistics' => $categoryStatistics,
    ]);
}
#[Route('/submit-rating', name: 'submit_rating', methods: ['POST'])]
public function submitRating(Request $request, EntityManagerInterface $entityManager): Response
{
    // Retrieve the user with ID 1
    $userId = 1;
    $userRepository = $entityManager->getRepository(User::class);
    $user = $userRepository->find($userId);
    
    // Retrieve the rating value and the product ID from the request
    $ratingValue = $request->request->get('rating');
    $productId = $request->request->get('productId');
    
    // Retrieve the product entity based on the provided product ID
    $product = $entityManager->getRepository(Produit::class)->find($productId);
    
    if (!$product) {
        // Handle the case where the product is not found
        // For example, return a JSON response with an error message
        return $this->json(['error' => 'Product not found'], 404);
    }

    // Create a new Ratings entity
    $rating = new Ratings();
    $rating->setRating($ratingValue);
    $rating->setProduit($product);

    // Associate the rating with the user
    $rating->setUser($user);
    
    // Persist the rating to the database
    $entityManager->persist($rating);
    $entityManager->flush();
    
    // Handle success (e.g., return a success response)
    return $this->json(['success' => true]);
}


#[Route('/update-rating/{productId}', name: 'update_rating')]
public function updateRating(Request $request, $productId, EntityManagerInterface $entityManager): JsonResponse
{
    // Retrieve the user with ID 1
    $userId = 1;
    $userRepository = $entityManager->getRepository(User::class);
    $user = $userRepository->find($userId);
    
    // Retrieve the product entity based on the provided product ID
    $product = $entityManager->getRepository(Produit::class)->find($productId);
    
    if (!$product) {
        return $this->json(['success' => false, 'message' => 'Product not found'], 404);
    }

    // Retrieve all ratings for the product
    $ratings = $product->getRatings();

    // Calculate the average rating for the product
    $totalRating = 0;
    $totalRatings = count($ratings);
    foreach ($ratings as $rating) {
        $totalRating += $rating->getRating();
    }

    $averageRating = $totalRatings > 0 ? $totalRating / $totalRatings : 0;
    
    // Update the product entity with the new average rating
    $product->setRating($averageRating);

    // Persist the updated product entity
    $entityManager->persist($product);
    $entityManager->flush();

    return $this->json(['success' => true, 'average_rating' => $averageRating]);
}



}