<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Form\UserChangePasswordFormType;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\FormError;

class UserController extends AbstractController
{
  #[Route('/user/my-account', name: 'app_user_my_account')]
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
  public function changeName(): Response
  {
    return $this->render('user/index.html.twig', [
      'user' => 1,
    ]);
  }

  #[Route('/user/change-password', name: 'app_user_change_password')]
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
  public function changeEmail(): Response
  {
    return $this->render('user/index.html.twig', [
      'user' => 1,
    ]);
  }

  #[Route('/user/change-photo', name: 'app_user_change_photo')]
  public function changePhoto(): Response
  {
    return $this->render('user/index.html.twig', [
      'user' => 1,
    ]);
  }

  private function renderUserHomePage(User $user) {
    return $this->render('user/index.html.twig', [
      'user' => $user,
    ]);
  }
}
