<?php

namespace App\Security;
use App\Entity\User; 
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Doctrine\ORM\EntityManagerInterface; // Ajoutez cette ligne
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UserAuthAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    private $entityManager;

    public function __construct(private UrlGeneratorInterface $urlGenerator,EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $request->getSession()->set(Security::LAST_USERNAME, $email);
        $password = $request->request->get('password', '');

        return new Passport(
            new UserBadge($email, function ($userIdentifier) {
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);
                if (!$user) {
                    throw new CustomUserMessageAuthenticationException('Email could not be found.');
                }

                if ($user->getIsReported()) {
                    throw new CustomUserMessageAuthenticationException('Votre compte a été signalé.');
                }

                return $user;
            }),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {


        $user = $token->getUser();

        // Vérifier si l'utilisateur est signalé
        if ($user->getIsReported()) {
            // Ici, vous pouvez personnaliser la réponse selon votre besoin.
            // Par exemple, rediriger l'utilisateur vers une page d'erreur,
            // ou afficher un message qui lui indique que son compte est signalé.
            // Assurez-vous d'avoir une route 'account_reported' définie dans votre application.
            return new RedirectResponse($this->urlGenerator->generate('app_login'));
        }


        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
    
        // Get the user role
        $userRole = $token->getUser()->getRoles()[0];

        // Use a switch case to redirect to the appropriate page
        switch($userRole) {
            case 'ROLE_ADMIN':
                $request->getSession()->set('user_id', $token->getUser()->getId());
                return new RedirectResponse($this->urlGenerator->generate('test'));
            case 'ROLE_SPORTIF':
                //store the user id in session
                $request->getSession()->set('user_id', $token->getUser()->getId());
                return new RedirectResponse($this->urlGenerator->generate('app_guest'));

                 case 'ROLE_NUTRITIONIST':
                //store the user id in session
                $request->getSession()->set('user_id', $token->getUser()->getId());
                return new RedirectResponse($this->urlGenerator->generate('app_guest'));

                case 'ROLE_COATCH':
                    //store the user id in session
                    $request->getSession()->set('user_id', $token->getUser()->getId());
                    return new RedirectResponse($this->urlGenerator->generate('app_guest'));



              ;
            default:
                return new RedirectResponse($this->urlGenerator->generate('app_guest'));
        }
    }
    

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
