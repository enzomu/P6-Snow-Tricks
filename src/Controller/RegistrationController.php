<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppCustomAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            $security->login($user, AppCustomAuthenticator::class, 'main');

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('test@example.com', 'Mail Bot'))
                    ->to((string) $user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            // do anything else you need here, like send an email

            $this->addFlash('success', 'Un email de confirmation a été envoyé. Veuillez vérifier vos emails.');
            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, EntityManagerInterface $em): Response
    {
        try {
            // Récupérer l'ID directement depuis la requête
            $id = $request->get('id');

            if (!$id) {
                throw new \RuntimeException('ID utilisateur manquant dans la requête');
            }

            $user = $em->getRepository(User::class)->find($id);

            if (!$user) {
                throw new \RuntimeException('Utilisateur non trouvé');
            }

            // Vérifier l'email - cette méthode va modifier isVerified et enregistrer l'utilisateur
            $this->emailVerifier->handleEmailConfirmation($request, $user);

            // Message de succès
            $this->addFlash('success', 'Votre email a été vérifié avec succès.');
            return $this->redirectToRoute('home');
        } catch (\RuntimeException $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_login');
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('error', 'Erreur de vérification: '.$translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
            return $this->redirectToRoute('home');
        }
    }
}
