<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

class SecurityController extends AbstractController
{
  #[IsGranted(new Expression('! is_authenticated()'))]
  #[Route(path: '/login', name: 'app_login')]
  public function login(AuthenticationUtils $authenticationUtils): Response
  {
    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('security/login.html.twig', [
      'lastUsername' => $lastUsername,
      'error' => $error,
    ]);
  }

  #[Route(path: '/logout', name: 'app_logout')]
  #[isGranted('IS_AUTHENTICATED')]
  public function logout(): void
  {
    // throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
  }
}
