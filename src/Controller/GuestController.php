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
            // Vérifier le rôle de l'utilisateur
            $roles = $user->getRoles();

            if (in_array('ROLE_SPORTIF', $roles)) {
                
                return $this->render('guest/index.html.twig', [
                    'users' => $userRepository->findAll(),
                    'user' => $user,
                ]);
            }
        }




       

        // Si l'utilisateur n'est pas trouvé ou n'a pas le rôle 'ROLE_SPORTIF', afficher la vue guest/index.html.twig
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
    
        // Récupérer les réclamations associées à cet utilisateur
        $reclamations = $user->getReclationID();
    
        // Supprimer chaque réclamation
        foreach ($reclamations as $reclamation) {
            $em->remove($reclamation);
        }
    
        // Enfin, supprimer l'utilisateur
        $em->remove($user);
        $em->flush();
    
      
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
      
    }


 





















    #[Route('/guest/edit', name: 'app_guest_edit')]
    public function edit(Request $request, UserRepository $userRepository): Response
    {
        $userId = $this->getUser()->getId();
        $user = $userRepository->getUserById($userId);
        $form = $this->createForm(GuestType::class, $user);
        $form->handleRequest($request);
        $img = $user->getImage();


        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form->get('name')->getData();
            $email = $form->get('email')->getData();
            $file = $form->get('image')->getData();
            if($file)
            {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                    //set img with image selected 
                    
                } catch (FileException $e){

                }
                $user->setImage($fileName);
            }
            $user->setName($name);
            $user->setEmail($email);

            // Save the updated user entity to the database
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_guest', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('guest/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


/*
    #[Route('/reclamationaa', name: 'reclamation')]
    public function creeReclamation(EntityManagerInterface $em,Request $req,ReclamationRepository $RR)
    {
        $reclamation = new Reclamation();
       $form=$this->createForm(AddEditReclamationType::class,$reclamation);

       $form->handleRequest($req);

       if ($form->isSubmitted() && $form->isValid()){
        $reclamation->setEtat(false);
        // Récupération du fichier image depuis le formulaire
        $imageFile = $form->get('image')->getData();
        // Vérification si un fichier image a été fourni
        if ($imageFile) {
            // Génération d'un nom de fichier unique basé sur le hachage md5 et l'extension du fichier
            $newFileName = md5(uniqid()) . '.' . $imageFile->guessExtension();
            // Déplacement du fichier image vers le répertoire de stockage
            $imageFile->move(
                'G:\ProjetWeb\ProjetWeb\ProjetWeb\public\assets\images', // Répertoire de stockage des images
                $newFileName
            );
            $reclamation->setImage($newFileName);
        } 
        $em->persist($reclamation);
        // Exécution des opérations d'écriture en base de données
        $em->flush();
        return $this->redirectToRoute('reclamation');
       }

     //  $listreclamation = $RR->findAll();


       return $this->render('guest/reclamation.html.twig', [
        'formAdd' => $form->createView(),  // Assurez-vous que la variable $form est bien définie dans votre contrôleur
       // 'reclamationf' => $listreclamation,
    ]);
        }*/

        
}