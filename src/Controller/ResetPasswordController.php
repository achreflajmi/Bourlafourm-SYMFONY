<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordType;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
class ResetPasswordController extends AbstractController
{
   // Cette annotation définit une route pour l'URL '/reset-password' et lui donne un nom 'reset_password'.
// Cela signifie que lorsque vous visitez http://votre-domaine.com/reset-password, cette méthode sera exécutée.
#[Route('/reset-password', name: 'reset_password')]
public function resetPassword(Request $request, EntityManagerInterface $entityManager , MailerInterface $mailer)
{
    $form = $this->createForm(ResetPasswordType::class);

    $form->handleRequest($request);

    if ($form->isSubmitted() ) {
      
        $data = $form->getData();
        
        $toemail = $data->getEmail();
        //récupérer un utilisateur par son e-mail
        $user = $entityManager->getRepository(User::class)->getUserByEmail($toemail);
        
        
        if ($user) {
            
            $resetCode = $this->generateResetCode();
            
            $user->setResetCode($resetCode);
            
            $entityManager->flush();

            // Crée le corps de l'e-mail en HTML.
            $html = '
            <html>
                <body>
                    <p>Hi user,</p>
                    <p>Someone has requested a link to change your password. You can do this through the link below.</p>
                    <p><a href="http://localhost:8070/verify-reset-code/'.$resetCode.'">Change my password</a></p>

                    <p>If you didn\'t request this, please ignore this email.</p>
                    <p>Your password won\'t change until you access the link above and create a new one.</p>
                </body>
            </html>
            ';
           
            $email = (new Email())
            ->from('yo.talent7@gmail.com')
            ->to($toemail)
            ->subject('Reset Password')
            ->html($html);
            $mailer->send($email);

            // Redirige l'utilisateur vers la page de connexion après l'envoi de l'e-mail.
            return $this->redirectToRoute('app_login');
        }
    }

    return $this->render('resetpassword/reset_password.html.twig', [
        'formReset' => $form->createView(),
    ]);
}

    #[Route('/verify-reset-code/{resetCode}', name: 'verify_reset_code')]
    public function verifyResetCode(Request $request, $resetCode, EntityManagerInterface $entityManager ,UserPasswordHasherInterface $userPasswordHasher)
    {
        // Find the user by the reset code
        $user = $entityManager->getRepository(User::class)->getUserByResetCode(['resetCode' => $resetCode]);
        if (!$user) {
            // Handle invalid or expired reset code
            return $this->redirectToRoute('reset_password');
        }

        // If the reset code is valid, render the password reset form
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);
        $data = $form->getData();


        if ($form->isSubmitted()) {
            if($data->getPassword() != $data->getConfirmPassword()){
                $this->addFlash('error', 'Password and confirm password does not match');
                return $this->redirectToRoute('verify_reset_code', ['resetCode' => $resetCode]);
            }
            // Update the user's password
            $hashedPassword = $userPasswordHasher->hashPassword($user, $data->getPassword());
            $user->setPassword($hashedPassword);
            $user->setResetCode(null);
            $entityManager->flush();

            // Redirect or render a success message
            return $this->redirectToRoute('app_login');
        }

        return $this->render('resetpassword/verify_reset_code.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function generateResetCode()
    {
        // Generate a unique reset code (you can customize the logic)
        return uniqid();
    }
}
