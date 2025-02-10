<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

class RegistrationController extends AbstractController
{
  public function __construct(
    private readonly EmailVerifier $emailVerifier,
  ) {}

  #[IsGranted(new Expression('! is_authenticated()'))]
  #[Route('/register', name: 'app_register')]
  public function register(
    Request $request,
    UserPasswordHasherInterface $userPasswordHasher,
    EntityManagerInterface $entityManager,
    Security $security,
  ): Response {
    $user = new User();
    $form = $this->createForm(RegistrationType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $plainPassword = $form->get('password')->getData();

      $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

      $entityManager->persist($user);
      $entityManager->flush();

      $this->_sendVerificationEmail($user);

      $security->login($user, 'security.authenticator.form_login.main');

      return $this->redirectToRoute('app_email_verification');
    }

    return $this->render('registration/register.html.twig', [
      'registrationForm' => $form,
    ]);
  }

  #[Route('/email/verification', name: 'app_email_verification', methods: ['GET', 'POST'])]
  #[isGranted('IS_AUTHENTICATED')]
  public function userEmailVerification(
    Request $request
  ): Response {
    /** @var User $user */
    $user = $this->getUser();

    if (!$user->emailVerified() && $request->getMethod() === 'POST') {
      $this->_sendVerificationEmail($user);
    }

    return $this->render('registration/email_verification.html.twig');
  }

  #[Route('/email/verify', name: 'app_verify_email')]
  #[isGranted('IS_AUTHENTICATED')]
  public function verifyUserEmail(
    Request $request,
    EntityManagerInterface $entityManager,
    TranslatorInterface $translator,
  ): Response {
    /** @var User $user */
    $user = $this->getUser();

    if (!$user->emailVerified()) {
      try {
        $this->emailVerifier->validateEmailConfirmationFromRequest($request, $user);

        $user->setEmailVerified(true);

        $entityManager->persist($user);
        $entityManager->flush();
      } catch (VerifyEmailExceptionInterface $exception) {
        $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
      }
    }

    return $this->redirectToRoute('app_email_verification');
  }

  private function _sendVerificationEmail(User $user): void
  {
    $this->emailVerifier->sendEmailConfirmation(
      'app_verify_email',
      $user,
      $this->emailVerifier->emailTemplate((string) $user->getEmail())
        ->subject('Please Confirm your Email')
        ->htmlTemplate('email/confirmation_email.html.twig')
    );
  }
}
