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
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Service\FileSystem;

class UserController extends AbstractController
{
  #[Route('/user/my-account', name: 'app_user_my_account')]
  #[IsGranted('IS_AUTHENTICATED')]
  public function myAccount(Security $security): Response
  {
    return $this->renderUserHomePage($security->getUser());
  }

  #[Route('/user/{user}', name: 'app_user', requirements: ['user' => '\d+'])]
  public function index(User $user): Response
  {
    return $this->renderUserHomePage($user);
  }

  #[Route('/user/change-name', name: 'app_user_change_name')]
  #[IsGranted('IS_AUTHENTICATED')]
  public function changeName(
    Request $request,
    EntityManagerInterface $entityManager,
    Security $security
  ): Response
  {
    $form = $this->createForm(UserChangeNameFormType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $nameField = $form->get('name');
      $name = $nameField->getData();

      /** @var User $user */
      $user = $security->getUser();

      if ($name !== $user->getName()) {
        $user->setName($name);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_user_my_account');
      } else {
        $nameField->addError(new FormError('You typed the same name as you already have'));
      }
    }

    return $this->render('user/changeNameForm.html.twig', [
      'form' => $form,
    ]);
  }

  #[Route('/user/change-password', name: 'app_user_change_password', methods: ['GET', 'POST'])]
  #[IsGranted('IS_AUTHENTICATED')]
  public function changePassword(
    Request $request,
    UserPasswordHasherInterface $userPasswordHasher,
    EntityManagerInterface $entityManager,
    Security $security
  ): Response
  {
    $form = $this->createForm(UserChangePasswordFormType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $password = $form->get('password');
      /** @var User $user */
      $user = $security->getUser();

      if ($userPasswordHasher->isPasswordValid($user, $password->getData())) {
        $user->setPassword($userPasswordHasher->hashPassword(
          $user,
          $form->get('newPassword')->getData())
        );
        $user->setPasswordChangedAt(new \DateTime());

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_user_my_account');
      }

      $password->addError(new FormError('Current password is not valid'));
    }

    return $this->render('user/changePasswordForm.html.twig', [
      'form' => $form,
    ]);
  }

  #[Route('/user/change-email', name: 'app_user_change_email')]
  #[IsGranted('IS_AUTHENTICATED')]
  public function changeEmail(
    Request $request,
    EntityManagerInterface $entityManager,
    Security $security
  ): Response
  {
    $form = $this->createForm(UserChangeEmailFormType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $emailField = $form->get('email');
      $email = $emailField->getData();

      /** @var User $user */
      $user = $security->getUser();

      if ($email !== $user->getEmail()) {
        $user->setEmail($email);
        $user->setVerified(false);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_user_my_account');
      } else {
        $emailField->addError(new FormError('You typed the same name as you already have'));
      }
    }

    return $this->render('user/changeEmailForm.html.twig', [
      'form' => $form,
    ]);
  }

  #[Route('/user/change-photo', name: 'app_user_change_photo', methods: ['GET', 'POST'])]
  #[IsGranted('IS_AUTHENTICATED')]
  public function changePhoto(
    Request $request,
    EntityManagerInterface $entityManager,
    Security $security
  ): Response
  {
    $form = $this->createForm(UserChangePhotoFormType::class);
    $form->handleRequest($request);

    /** @var User $user */
    $user = $security->getUser();
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
        return $this->redirectToRoute('app_user_my_account');
      }
    }

    return $this->render('user/changePhotoForm.html.twig', [
      'form' => $form,
      'photo' => $photo ? "$photosDir/$photo" : null
    ]);
  }

  private function renderUserHomePage(User $user) {
    return $this->render('user/index.html.twig', [
      'user' => $user,
    ]);
  }
}
