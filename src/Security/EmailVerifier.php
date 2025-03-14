<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Component\Mime\Address;

class EmailVerifier
{
  public function __construct(
    private VerifyEmailHelperInterface $verifyEmailHelper,
    private MailerInterface $mailer,
  ) {}

  public function sendEmailConfirmation(
    string $verifyEmailRouteName,
    User $user,
    TemplatedEmail $email,
  ): void {
    $signatureComponents = $this->verifyEmailHelper->generateSignature(
      $verifyEmailRouteName,
      (string) $user->getId(),
      (string) $user->getEmail()
    );

    $context = $email->getContext();
    $context['signedUrl'] = $signatureComponents->getSignedUrl();
    $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
    $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

    $email->context($context);

    $this->mailer->send($email);
  }

  public function emailTemplate(string $email): TemplatedEmail
  {
    return (new TemplatedEmail())
      ->from(new Address('mailer@symfony-books.com', 'Symfony Books'))
      ->to($email);
  }

  /**
   * @throws VerifyEmailExceptionInterface
   */
  public function validateEmailConfirmationFromRequest(
    Request $request,
    User $user,
  ): void {
    $this->verifyEmailHelper->validateEmailConfirmationFromRequest(
      $request,
      (string) $user->getId(),
      (string) $user->getEmail(),
    );
  }
}
