<?php

namespace Test\Security;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Component\Mime\Address;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Security\EmailVerifier;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;
use PHPUnit\Framework\MockObject\MockObject;

final class EmailVerifierTest extends WebTestCase
{
  public function testSendEmailConfirmation(): void
  {
    $verifyEmailHelper = $this->getEmailVerifier();
    $mailer = $this->getMailer();
    /** @var MockObject&TemplatedEmail $email */
    $email = $this->createMock(TemplatedEmail::class);
    $signatureComponents = new VerifyEmailSignatureComponents(
      new \DateTime(),
      'uri',
      2,
    );
    $user = new User();
    $routeName = 'route_name';
    $context = [];

    $emailVerifier = new EmailVerifier(
      $verifyEmailHelper,
      $mailer,
    );

    $verifyEmailHelper
      ->expects($this->once())
      ->method('generateSignature')
      ->with(
        $routeName,
        (string) $user->getId(),
        (string) $user->getEmail(),
      )
      ->willReturn($signatureComponents);

    $email
      ->expects($this->once())
      ->method('getContext')
      ->willReturn($context);

    $context['signedUrl'] = $signatureComponents->getSignedUrl();
    $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
    $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

    $email
      ->expects($this->once())
      ->method('context')
      ->with($context)
      ->willReturn($email);

    $mailer
      ->expects($this->once())
      ->method('send')
      ->with($email);

    $emailVerifier->sendEmailConfirmation(
      $routeName,
      $user,
      $email,
    );
  }

  public function testEmailTemplate(): void
  {
    $verifyEmailHelper = $this->getEmailVerifier();
    $mailer = $this->getMailer();
    $email = 'email@email.com';

    $emailVerifier = new EmailVerifier(
      $verifyEmailHelper,
      $mailer,
    );

    $emailTemplate = $emailVerifier->emailTemplate($email);

    $this->assertInstanceOf(TemplatedEmail::class, $emailTemplate);
    $this->assertEquals(
      [new Address('mailer@symfony-books.com', 'Symfony Books')],
      $emailTemplate->getFrom(),
    );
    $this->assertEquals(
      [new Address($email, '')],
      $emailTemplate->getTo(),
    );
  }

  private function getEmailVerifier(): MockObject&VerifyEmailHelperInterface
  {
    return $this->createMock(VerifyEmailHelperInterface::class);
  }

  private function getMailer(): MockObject&MailerInterface
  {
    return $this->createMock(MailerInterface::class);
  }

  // public function testValidateEmailConfirmationFromRequest(): void
  // {
  //   $verifyEmailHelper = $this->createMock(VerifyEmailHelperInterface::class);
  //   $mailer = $this->createMock(MailerInterface::class);
  //   $request = new Request;
  //   $user = new User;

  //   $verifyEmailHelper
  //     ->expects($this->once())
  //     ->method('validateEmailConfirmationFromRequest')
  //     ->with(
  //       $request,
  //       (string) $user->getId(),
  //       (string) $user->getEmail(),
  //     );

  //   $emailVerifier = new EmailVerifier(
  //     $verifyEmailHelper,
  //     $mailer,
  //   );

  //   $emailVerifier->validateEmailConfirmationFromRequest($request, $user);
  // }
}
