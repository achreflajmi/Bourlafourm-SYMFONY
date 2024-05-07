<?php

namespace App\Controller;



use App\Form\GuestType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


use App\Form\AddEditReclamationType;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Reclamation;
use App\Entity\User;


class GuestController extends AbstractController
{


    #[Route('/guest', name: 'app_guest')]
    public function index(UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $userId = $user->getId();

        // Récupérer l'utilisateur depuis le UserRepository
        $user = $userRepository->find($userId);

        if ($user) {
            
            $roles = $user->getRoles();

            if (in_array('ROLE_SPORTIF', $roles)) {
                
                return $this->render('guest/index.html.twig', [
                    'users' => $userRepository->findAll(),
                    'user' => $user,
                ]);
            }
        }


        return $this->render('guest/index.html.twig', [
            'user' => $user,
            'users' => [],
           
        ]);
        
    }


    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $em,Request $request, $id, UserRepository $userRepository,AuthenticationUtils $authenticationUtils): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé avec l\'identifiant : ' . $id);
        }
    
        $reclamations = $user->getReclationID();
    
        foreach ($reclamations as $reclamation) {
            $em->remove($reclamation);
        }
    
        $em->remove($user);
        $em->flush();
    
      
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
      
    }


 


    #[Route('/guest/edit', name: 'app_guest_edit')]
    public function edit(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder): Response
    {
    $userId = $this->getUser()->getId();
    $user = $userRepository->getUserById($userId);
    $form = $this->createForm(GuestType::class, $user);
    $form->handleRequest($request);
    $img = $user->getImage();

    if ($form->isSubmitted() && $form->isValid()) {
        $nom = $form->get('nom')->getData();
        $prenom = $form->get('prenom')->getData();
        $email = $form->get('email')->getData();
        $file = $form->get('image')->getData();

        
        $newPassword = $form->get('password')->getData();
        if (!empty($newPassword)) {
            $encodedPassword = $passwordEncoder->encodePassword($user, $newPassword);
            $user->setPassword($encodedPassword);
        }

        if ($file) {
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
                
                
            } catch (FileException $e) {
            }
            $user->setImage($fileName);
        }

        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setEmail($email);

        // Enregistrer l'entité utilisateur mise à jour dans la base de données
        $userRepository->save($user, true);

        return $this->redirectToRoute('app_guest', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('guest/edit.html.twig', [
        'user' => $user,
        'form' => $form,
    ]);
    }
    



        
}