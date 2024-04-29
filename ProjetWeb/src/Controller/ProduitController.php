<?php

namespace App\Controller;
use App\Entity\Produit;
use App\Entity\Ratings;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use App\Entity\Categorie;
use App\Form\ProduitType;
use App\Repository\UserRepository;
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
        $query = $this->getDoctrine()->getManager()->getRepository(Produit::class)->createQueryBuilder('p');
    
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            6
        );
        return $this->render('Front/produit/index.html.twig', [
           'pagination' => $pagination,
           'user' => $user,
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

// #[Route('/submit_rating', name: 'submit_rating', methods: ['POST'])]
// public function submitRating2(Request $request, UserRepository $userRepository, EntityManagerInterface $em): Response
// {
//     $userId = 1;
//     $user = $userRepository->find($userId);

//     // Get the product ID and rating from the form submission
//     $id = $request->request->getInt('id_prod');
//     $ratingValue = $request->request->get('rating');

//     // Check if the product ID is missing
//     if ($id === 0) {
//         // Handle the case where the product ID is missing
//         throw $this->createNotFoundException('Product ID is missing');
//     }

//     // Retrieve the product entity based on the ID
//     $product = $em->getRepository(Produit::class)->find($id);

//     // Check if the product exists
//     if (!$product) {
//         // Handle the case where the product does not exist
//         throw $this->createNotFoundException('Product not found for ID: ' . $id);
//     }

//     // Create a new Ratings entity and set its properties
//     $rating = new Ratings();
//     $rating->setProduit($product);
//     $rating->setRating($ratingValue);
//     $rating->setUser($user);

//     // Persist the rating entity
//     $em->persist($rating);
//     $em->flush();

//     return $this->redirectToRoute('display_produit');
// }

#[Route('/update-rating/{productId}', name: 'update_rating')]
public function updateRating(Request $request, $productId, EntityManagerInterface $entityManager, LoggerInterface $logger): JsonResponse
{
    try {
        $rating = (float) $request->request->get('rating');
        $produit = $entityManager->getRepository(Produit::class)->find($productId);

        if (!$produit instanceof Produit) {
            return $this->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        // Update the rating of the product
        $produit->setRating($rating);

        $entityManager->persist($produit);
        $entityManager->flush();

        return $this->json(['success' => true]);
    } catch (\Exception $e) {
        $errorMessage = $e->getMessage();
        $logger->error('Error updating rating: ' . $errorMessage);
        return $this->json(['success' => false, 'message' => 'Internal Server Error'], 500);
    }
}

}
