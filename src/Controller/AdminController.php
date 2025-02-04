<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Enum\UserRole;
use App\Form\ChangeUserRoleType;

#[Route('/admin', name: 'app_admin_')]
class AdminController extends AbstractController
{
  #[Route('/panel', name: 'panel')]
  #[IsGranted(UserRole::Admin->value)]
  public function changeUserRole(
    Request $request,
    EntityManagerInterface $entityManager,
  ): Response
  {
    $form = $this->createForm(ChangeUserRoleType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $role = $form->get('role')->getData();
      $user = $form->get('user')->getData();

      if ($user !== $this->getUser()) {
        $user->setRole($role);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_user_my_account');
      }
    }

    return $this->render('admin/panel.html.twig', [
      'form' => $form,
    ]);
  }
}
