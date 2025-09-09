<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Entity\User;
use App\Enum\UserRole;
use App\Form\UserChangeNameType;
use App\Form\UserChangePasswordType;
use App\Form\UserChangeEmailType;
use App\Form\UserDeleteAccountType;
use App\Form\UserChangePhotoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Service\FileSystem;
use App\Security\EmailVerifier;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Bundle\SecurityBundle\Security;

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
  ): Response {
    /** @var User $user */
    $user = $this->getUser();
    $name = $user->getName();

    $form = $this->createForm(UserChangeNameType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $nameField = $form->get('name');

      if ($name !== $user->getName()) {
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_user_my_account');
      } else {
        $nameField->addError(new FormError('same.name'));
      }
    }

    // this must exists to not change user name in views when form is not valid
    $user->setName($name);

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
  ): Response {
    $form = $this->createForm(UserChangePasswordType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      /** @var User $user */
      $user = $this->getUser();

      $user->setPassword($userPasswordHasher->hashPassword(
        $user,
        $form->get('newPassword')->getData()
      ));
      $user->setPasswordChangedAt(new \DateTime());

      $entityManager->persist($user);
      $entityManager->flush();

      return $this->redirectToRoute('app_user_my_account');
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
    TranslatorInterface $translator,
  ): Response {
    /** @var User $user */
    $user = $this->getUser();

    if ($user->isNewEmailSet()) {
      return $this->redirectToRoute('app_user_email_change_verification');
    }

    $form = $this->createForm(UserChangeEmailType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $newEmailInput = $form->get('email');
      $newEmailFromRequest = $newEmailInput->getData();

      if ($newEmailFromRequest !== $user->getEmail()) {
        $this->_sendChangeEmailVerification($emailVerifier, $user, $translator);

        if ($user->getNewEmail() !== $newEmailFromRequest) {
          $user->setNewEmail($newEmailFromRequest);
          $user->setDefaultNewEmailExpires();

          $entityManager->persist($user);
          $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_email_change_verification');
      } else {
        $newEmailInput->addError(new FormError('same.email'));
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
  ): Response {
    /** @var User $user */
    $user = $this->getUser();

    if (!$user->isNewEmailSet()) {
      $this->addFlash('change_email_error', $translator->trans('empty_new_email'));

      return $this->redirectToRoute('app_user_change_email');
    }

    $verificationLink = true;
    $error = false;

    try {
      $emailVerifier->validateEmailConfirmationFromRequest($request, $user);

      $user->setEmail($user->getNewEmail());
      $user->setNewEmail(null);
      $user->setNewEmailExpires(null);
      $user->setEmailVerified(false);

      $entityManager->persist($user);
      $entityManager->flush();
    } catch (VerifyEmailExceptionInterface $e) {
      $verificationLink = false;
    } catch (\Exception $e) {
      $error = true;
    }

    return $this->render('user/set_new_email.html.twig', [
      'verificationLink' => $verificationLink,
      'error' => $error,
    ]);
  }

  #[Route('/email-change-verification', name: 'email_change_verification')]
  #[isGranted('IS_AUTHENTICATED')]
  public function userEmailVerification(
    Request $request,
    EmailVerifier $emailVerifier,
    TranslatorInterface $translator,
  ): Response {
    /** @var User $user */
    $user = $this->getUser();

    if (!$user->isNewEmailSet()) {
      return $this->redirectToRoute('app_user_change_email');
    }

    if ($request->getMethod() === 'POST') {
      if ($this->isCsrfTokenValid(
        'resend_email',
        $request->getPayload()->getString('_token')
      )) {
        $this->_sendChangeEmailVerification($emailVerifier, $user, $translator);
      }
    }

    return $this->render('user/change_email_verification.html.twig');
  }

  #[Route('/change-photo', name: 'change_photo', methods: ['GET', 'POST'])]
  #[IsGranted('IS_AUTHENTICATED')]
  public function changePhoto(
    Request $request,
    EntityManagerInterface $entityManager,
  ): Response {
    $form = $this->createForm(UserChangePhotoType::class);
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
        $newPhoto != null => 'user' . '-' . \bin2hex(random_bytes(13)) . '.' . $newPhoto->guessExtension(),
        default => $photo
      });

      try {
        $entityManager->flush();
      } catch (ORMException $e) {
        $form->addError(new FormError('form.general'));
      }

      $photoDir = FileSystem::getDocumentRoot().$photosDir;

      if ($photo) {
        if ($deletePhoto) {
          FileSystem::deleteDir($photoDir);
        } else if ($newPhoto) {
          FileSystem::deleteFile($photoDir."/".$photo);
        }
      }

      if ($newPhoto) {
        try {
          $newPhoto->move($photoDir, $user->getPhoto());
        } catch (FileException $e) {
          $form->addError(new FormError('file.move'));
        }
      }

      if (!\count($form->getErrors())) {
        return $this->redirectToRoute('app_user_my_account');
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
    TranslatorInterface $translator,
  ): Response {
    /** @var User $user */
    $user = $this->getUser();

    if ($user->isNewEmailSet()) {
      if ($this->isCsrfTokenValid(
        'cancel_email_change',
        $request->getPayload()->getString('_token')
      )) {
        try {
          $user->setNewEmail(null);
          $entityManager->persist($user);
          $entityManager->flush();
        } catch (\Exception $e) {
          $this->addFlash('change_email_error', $translator->trans('cancel_email_change_database_error'));

          return $this->redirectToRoute('app_user_email_change_verification');
        }
      }
    }

    return $this->redirectToRoute('app_user_my_account');
  }

  #[Route('/delete-account', name: 'delete_account', methods: [ 'GET', 'POST'])]
  #[IsGranted(new Expression('is_authenticated() & !is_granted("'.UserRole::Admin->value.'")'))]
  public function deleteAccount(
    Request $request,
    EntityManagerInterface $entityManager,
    Security $security,
  ): Response {
    $form = $this->createForm(UserDeleteAccountType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      /** @var User $user */
      $user = $this->getUser();

      if ($user->getPhoto()) {
        FileSystem::deleteDir(FileSystem::getDocumentRoot().$user->getPhotosDir());
      }

      $entityManager->remove($user);
      $entityManager->flush();

      $security->logout(false);

      return $this->redirectToRoute('app_home');
    }

    return $this->render('user/_delete_form.html.twig', [
      'form' => $form,
    ]);
  }

  private function _renderUserHomePage(User $user) {
    return $this->render('user/index.html.twig', [
      'user' => $user,
    ]);
  }

  private function _sendChangeEmailVerification(
    EmailVerifier $emailVerifier,
    User $user,
    TranslatorInterface $translator,
  ): void {
    $emailVerifier->sendEmailConfirmation(
      'app_user_set_new_email',
      $user,
      $emailVerifier
        ->emailTemplate((string) $user->getEmail())
        ->subject($translator->trans('confirm_email_change_subject'))
        ->htmlTemplate('email/confirmation_change_email.html.twig'),
    );
  }
}
