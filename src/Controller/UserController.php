<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Entity\User;
use App\Form\UserChangeNameFormType;
use App\Form\UserChangePasswordFormType;
use App\Form\UserChangeEmailFormType;
use App\Form\UserChangePhotoFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Service\FileSystem;
use App\Security\EmailVerifier;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

#[Route('/user', name: 'app_user_')]
class UserController extends AbstractController
{
  #[Route('/my-account', name: 'my_account')]
  #[IsGranted('IS_AUTHENTICATED')]
  public function myAccount(): Response
  {
    return $this->_renderUserHomePage($this->getUser());
  }

  #[Route('/{user}', name: 'show', requirements: ['user' => '\d+'])]
  public function index(User $user): Response
  {
    return $this->_renderUserHomePage($user);
  }

  #[Route('/change-name', name: 'change_name')]
  #[IsGranted('IS_AUTHENTICATED')]
  public function changeName(
    Request $request,
    EntityManagerInterface $entityManager,
  ): Response
  {
    $form = $this->createForm(UserChangeNameFormType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $nameField = $form->get('name');
      $name = $nameField->getData();

      /** @var User $user */
      $user = $this->getUser();

      if ($name !== $user->getName()) {
        $user->setName($name);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('my_account');
      } else {
        $nameField->addError(new FormError('You typed the same name as you already have'));
      }
    }

    return $this->render('user/change_name_form.html.twig', [
      'form' => $form,
    ]);
  }

  #[Route('/change-password', name: 'change_password', methods: ['GET', 'POST'])]
  #[IsGranted('IS_AUTHENTICATED')]
  public function changePassword(
    Request $request,
    UserPasswordHasherInterface $userPasswordHasher,
    EntityManagerInterface $entityManager,
  ): Response
  {
    $form = $this->createForm(UserChangePasswordFormType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $password = $form->get('password');
      /** @var User $user */
      $user = $this->getUser();

      if ($userPasswordHasher->isPasswordValid($user, $password->getData())) {
        $user->setPassword($userPasswordHasher->hashPassword(
          $user,
          $form->get('newPassword')->getData())
        );
        $user->setPasswordChangedAt(new \DateTime());

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('my_account');
      }

      $password->addError(new FormError('Current password is not valid'));
    }

    return $this->render('user/change_password_form.html.twig', [
      'form' => $form,
    ]);
  }

  #[Route('/change-email', name: 'change_email')]
  #[IsGranted('IS_AUTHENTICATED')]
  public function changeEmail(
    Request $request,
    EntityManagerInterface $entityManager,
    EmailVerifier $emailVerifier,
  ): Response
  {
    /** @var User $user */
    $user = $this->getUser();
    $newEmail = $user->getNewEmail();

    if ($newEmail) {
      return $this->redirectToRoute('app_email_change_verification');
    }

    $form = $this->createForm(UserChangeEmailFormType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $emailField = $form->get('email');
      $email = $emailField->getData();

      if ($email !== $user->getEmail()) {
        $this->_sendChangeEmailVerification($emailVerifier, $user);

        if (!$newEmail || $newEmail !== $email) {
          $user->setNewEmail($email);

          $entityManager->persist($user);
          $entityManager->flush();
        }

        return $this->redirectToRoute('app_email_change_verification');
      } else {
        $emailField->addError(new FormError('You typed the same email as you already have'));
      }
    }

    return $this->render('user/change_email_form.html.twig', [
      'form' => $form,
    ]);
  }

  #[Route('/set-new-email', name: 'set_new_email')]
  #[IsGranted('IS_AUTHENTICATED')]
  public function setNewEmail(
    Request $request,
    EntityManagerInterface $entityManager,
    EmailVerifier $emailVerifier,
    TranslatorInterface $translator,
  ): Response
  {
    /** @var User $user */
    $user = $this->getUser();
    $newEmail = $user->getNewEmail();

    if (! $newEmail) {
      $this->addFlash('change_email_error', $translator->trans('empty_new_email'));

      return $this->redirectToRoute('app_email_change_verification');
    }

    try {
      $emailVerifier->validateEmailConfirmationFromRequest($request, $user);

      $user->setEmail($newEmail);
      $user->setNewEmail(null);
      $user->setEmailVerified(false);

      $entityManager->persist($user);
      $entityManager->flush();
    } catch (VerifyEmailExceptionInterface $exception) {
      $this->addFlash('change_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
    }

    return $this->redirectToRoute('app_email_change_verification');
  }

  #[Route('/email/change-verification', name: 'app_email_change_verification')]
  #[isGranted('IS_AUTHENTICATED')]
  public function userEmailVerification(
    Request $request,
    EmailVerifier $emailVerifier,
  ): Response
  {
    /** @var User $user */
    $user = $this->getUser();

    if ($user->getNewEmail() && $request->getMethod() === 'POST') {
      if ($this->isCsrfTokenValid(
        'resend_email',
        $request->getPayload()->getString('_token')
      )) {
        $this->_sendChangeEmailVerification($emailVerifier, $user);
      }
    }

    return $this->render('user/change_email_verification.html.twig');
  }

  #[Route('/change-photo', name: 'change_photo', methods: ['GET', 'POST'])]
  #[IsGranted('IS_AUTHENTICATED')]
  public function changePhoto(
    Request $request,
    EntityManagerInterface $entityManager,
  ): Response
  {
    $form = $this->createForm(UserChangePhotoFormType::class);
    $form->handleRequest($request);

    /** @var User $user */
    $user = $this->getUser();
    $photo = $user->getPhoto();
    $photosDir = $user->getPhotosDir();

    if ($form->isSubmitted() && $form->isValid()) {
      /** @var Symfony\Component\HttpFoundation\File $newPhoto */
      $newPhoto = $form->get('photo')->getData();
      $deletePhoto = $form->has('deletePhoto') ? $form->get('deletePhoto')->getData() : false;

      $user->setPhoto(match (true) {
        $deletePhoto => null,
        $newPhoto != null => 'user' . '-' . bin2hex(random_bytes(13)) . '.' . $newPhoto->guessExtension(),
        default => $photo
      });

      try {
        $entityManager->flush();
      } catch (ORMException $e) {
        $form->addError(new FormError('Error occured while saving data'));
      }

      $photoDir = FileSystem::getDocumentRoot().$photosDir;

      if ($photo && ($deletePhoto || $newPhoto)) {
        FileSystem::deleteFile("$photoDir/$photo");
      }

      if ($newPhoto) {
        try {
          $newPhoto->move($photoDir, $user->getPhoto());
        } catch (FileException $e) {
          $form->addError(new FormError('Error occured while moving uploaded photo'));
        }
      }

      if (! count($form->getErrors())) {
        return $this->redirectToRoute('my_account');
      }
    }

    return $this->render('user/change_photo_form.html.twig', [
      'form' => $form,
      'photo' => $photo ? "$photosDir/$photo" : null
    ]);
  }

  #[Route('/cancel-email-change', name: 'cancel_email_change', methods: [ 'POST'])]
  #[IsGranted('IS_AUTHENTICATED')]
  public function cancelEmailChange(
    Request $request,
    EntityManagerInterface $entityManager,
  ): Response
  {
    /** @var User $user */
    $user = $this->getUser();

    if ($user->getNewEmail()) {
      if ($this->isCsrfTokenValid(
        'cancel_email_change',
        $request->getPayload()->getString('_token')
      )) {
        try {
          $user->setNewEmail(null);
          $entityManager->persist($user);
          $entityManager->flush();
        } catch (Exception $e) {
          return $this->redirectToRoute('app_email_change_verification');
        }
      }
    }

    return $this->redirectToRoute('my_account');
  }

  private function _renderUserHomePage(User $user) {
    return $this->render('user/index.html.twig', [
      'user' => $user,
    ]);
  }

  private function _sendChangeEmailVerification(
    EmailVerifier $emailVerifier,
    User $user,
  ): void
  {
    $emailVerifier->sendEmailConfirmation(
      'set_new_email',
      $user,
      $emailVerifier->emailTemplate((string) $user->getEmail())
        ->subject('Please Confirm your Change Email request')
        ->htmlTemplate('email/confirmation_change_email.html.twig')
    );
  }
}
