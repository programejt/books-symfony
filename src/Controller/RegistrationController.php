<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
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
    private EmailVerifier $emailVerifier
  ) {}

  #[IsGranted(new Expression('! is_authenticated()'))]
  #[Route('/register', name: 'app_register')]
  public function register(
    Request $request,
    UserPasswordHasherInterface $userPasswordHasher,
    EntityManagerInterface $entityManager,
    Security $security
  ): Response
  {
    $user = new User();
    $form = $this->createForm(RegistrationFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      /** @var string $plainPassword */
      $plainPassword = $form->get('password')->getData();

      $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

      $entityManager->persist($user);
      $entityManager->flush();

      $this->_sendVerificationEmail($user);

      $security->login($user);

      return $this->redirectToRoute('app_email_verification');
    }

    return $this->render('registration/register.html.twig', [
      'registrationForm' => $form,
    ]);
  }

  #[Route('/email/verification', name: 'app_email_verification')]
  public function userEmailVerification(
    Request $request
  ): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    /** @var User $user */
    $user = $this->getUser();

    if (!$user->isVerified() && $request->getMethod() === 'POST') {
      $this->_sendVerificationEmail($user);
    }

    return $this->render('registration/email_verification.html.twig');
  }

  #[Route('/email/verify', name: 'app_verify_email')]
  public function verifyUserEmail(
    Request $request,
    TranslatorInterface $translator
  ): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    /** @var User $user */
    $user = $this->getUser();

    if (!$user->isVerified()) {
      try {
        $this->emailVerifier->handleEmailConfirmation($request, $user);
      } catch (VerifyEmailExceptionInterface $exception) {
        $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
      }
    }

    return $this->redirectToRoute('app_email_verification');
  }

  private function _sendVerificationEmail(User $user): void {
    $this->emailVerifier->sendEmailConfirmation(
      'app_verify_email',
      $user,
      (new TemplatedEmail())
        ->from(new Address('mailer@symfony-books.com', 'Symfony Books'))
        ->to((string) $user->getEmail())
        ->subject('Please Confirm your Email')
        ->htmlTemplate('registration/confirmation_email.html.twig')
    );
  }
}
