<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $roles = $this->getUser()->getRoles();
            if (in_array('ROLE_ADMIN', $roles)) {
                return $this->redirectToRoute('test');
            } else {
                return $this->redirectToRoute('app_guest');
            }
        }
        

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(UrlGeneratorInterface $urlGenerator): RedirectResponse
    {
        $logoutUrl = $urlGenerator->generate('app_login');
        return new RedirectResponse($logoutUrl);    }
}
