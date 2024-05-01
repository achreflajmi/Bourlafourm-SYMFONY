<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Writer;
use App\Repository\ReclamationRepository;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Form\ReponseType;
use App\Repository\ReponseRepository;
use App\Entity\Reponse;


#[Route('/admin')]
class UserController extends AbstractController
{
    #[Route('/all', name: 'test', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }




    #[Route('/reponse/edit/{id}', name: 'editReponceAdmin', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function editAdminRePONCE(Request $request, int $id, ReponseRepository $reponseRepository): Response
    {
        // Charger l'entité Reponse à partir du ReponseRepository
        $reponse = $reponseRepository->find($id);

        // Vérifier si l'entité Reponse existe
        if (!$reponse) {
            throw $this->createNotFoundException('Réponse non trouvée pour l\'id : ' . $id);
        }

        // Créer le formulaire pour l'édition de l'entité Reponse
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        // Traiter le formulaire lorsqu'il est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer les modifications dans la base de données
            $reponseRepository->save($reponse, true);

            // Rediriger vers une autre page après l'édition réussie
            return $this->redirectToRoute('app_reclamation_showAdmin', [], Response::HTTP_SEE_OTHER);
        }

        // Afficher le formulaire d'édition dans le template Twig
        return $this->render('user/editReponceAdmin.html.twig', [
            'reponse' => $reponse,
            'form' => $form->createView(),
        ]);
    }





    #[Route('/reclamation/show', name: 'app_reclamation_showAdmin')]
    public function showReclamAdmin(ReclamationRepository $RR): Response
    {
        
        $listreclamation = $RR->findAll();

        return $this->render('user/listeReclamationAdmin.html.twig', [
            'controller_name' => 'ReclamationController',
            'reclamationsf' => $listreclamation,
        ]);
    }


    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager ,Request $request, UserRepository $userRepository,UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if($file)
            {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                } catch (FileException $e){

                }
                $user->setImage($fileName);
            }

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show($id, Request $request, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_user_edit', methods: ['GET', 'POST'])]
public function edit($id, Request $request, UserRepository $userRepository): Response
{
    $user = $userRepository->find($id);

    if (!$user) {
        throw $this->createNotFoundException('No user found for id '.$id);
    }

    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Traitement du formulaire

        // Si le fichier est présent
        $file = $form->get('image')->getData();
        if ($file) {
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            try {
                $file->move($this->getParameter('images_directory'), $fileName);
            } catch (FileException $e) {
                // Gérer l'exception si le fichier n'a pas pu être téléchargé
            }
            $user->setImage($fileName);
        } else {
            // Si aucun fichier n'est téléchargé, utilisez l'image existante
            $fileName = $user->getImage();
        }

        // Mettre à jour l'utilisateur
        $userRepository->updateUser($id, $form->get('name')->getData(), $form->get('email')->getData(), $fileName);

        return $this->redirectToRoute('test', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('user/edit.html.twig', [
        'user' => $user,
        'form' => $form,
    ]);
}

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
       
            $userRepository->remove($user, true);
        

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    public function generate_qr_code(User $user)
    {
        $renderer = new Png();
        $renderer->setHeight(256);
        $renderer->setWidth(256);

        $writer = new Writer($renderer);
        $qrCode = $writer->writeString('User ID: ' . $user->getId() . ', Email: ' . $user->getEmail());

        return 'data:image/png;base64,' . base64_encode($qrCode);
    }

    
    #[Route('/r/search_user', name: 'search_user', methods: ['GET'])]
    public function search_user(Request $request, NormalizerInterface $Normalizer, UserRepository $userRepository): Response
    {

        $requestString = $request->get('searchValue');
        $requestString3 = $request->get('orderid');

        $user = $userRepository->findUser($requestString, $requestString3);
        $jsoncontentc = $Normalizer->normalize($user, 'json', ['users' => 'posts:read']);
        $jsonc = json_encode($jsoncontentc);
        if ($jsonc == "[]") {
            return new Response(null);
        } else {
            return new Response($jsonc);
        }
    }




    #[Route('/report/user/{id}', name: 'report_user', methods: ['GET'])]
    public function report(int $id, EntityManagerInterface $entityManager,UserRepository $userRepository): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Aucun utilisateur trouvé pour cet id '.$id);
        }

        $user->setIsReported(true);
        $entityManager->flush();

        
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }




}