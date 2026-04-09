<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Security\LoginFormAuthenticator;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //  Hash du mot de passe
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            //  Générer token
            $user->setVerificationToken(Uuid::v4()->toRfc4122());

            // ❗ important
            $user->setIsVerified(false);

            //  sauvegarde
            $em->persist($user);
            $em->flush();

            //  Email de confirmation
            $email = (new TemplatedEmail())
                ->from(new Address('smartbrief.me@gmail.com', 'Stubborn'))
                ->to($user->getEmail())
                ->subject('Confirmez votre inscription')
                ->htmlTemplate('emails/registration_confirmation.html.twig')
                ->context([
                    'user' => $user,
                    'verificationUrl' => $this->generateUrl('app_verify_email', [
                        'token' => $user->getVerificationToken(),
                    ], UrlGeneratorInterface::ABSOLUTE_URL),
                ]);

              
                   try {
                        // Envoi direct du mail (pas via Messenger)
                        $mailer->send($email);
                    } catch (\Exception $e) {
                        // Affiche directement l'erreur dans le navigateur pour débogage
                        dd($e->getMessage());
                    }
                

            $this->addFlash('success', 'Inscription réussie ! Vérifiez votre email.');

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


    #[Route('/verify/email/{token}', name: 'app_verify_email')]
    public function verifyUserEmail(
        string $token,
        Request $request,
        EntityManagerInterface $em,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $authenticator
    ): Response {

        $user = $em->getRepository(User::class)
            ->findOneBy(['verificationToken' => $token]);

        if (!$user) {
            $this->addFlash('danger', 'Lien invalide.');
            return $this->redirectToRoute('app_register');
        }

        // activer compte
        $user->setIsVerified(true);
        $user->setVerificationToken(null);

        $em->flush();


        $this->addFlash('success', 'Compte activé ! Vous êtes connecté.');
         
       // Connexion automatique
    return $userAuthenticator->authenticateUser(
        $user,
        $authenticator,
        $request // il faudra injecter Request
    );
        

        // return $this->redirectToRoute('home');
    }
}