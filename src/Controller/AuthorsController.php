<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/authors', name: 'app_authors_')]
final class AuthorsController extends AbstractController
{
  #[Route(name: 'index', methods: ['GET'])]
  public function index(
    Request $request,
    AuthorRepository $authorRepository,
  ): Response {
    $currentPage = (int) $request->get('page', 1);
    $name = $request->get('name');

    if ($currentPage < 1) {
      $currentPage = 1;
    }

    $limit = 12;

    $authors = $authorRepository->findPaginated($name, $currentPage, $limit);

    return $this->render('authors/index.html.twig', [
      'searchValue' => $name,
      'currentPage' => $currentPage,
      'pagesCount' => ceil($authors->count() / $limit),
      'authors' => $authors,
    ]);
  }

  #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
  public function show(Author $author): Response
  {
    return $this->render('authors/show.html.twig', [
      'author' => $author,
    ]);
  }

  #[Route('/new', name: 'new', methods: ['GET', 'POST'], priority: 2)]
  #[IsGranted('IS_AUTHENTICATED')]
  public function new(
    Request $request,
    EntityManagerInterface $entityManager,
  ): Response {
    $author = new Author();
    $form = $this->createForm(AuthorType::class, $author);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->persist($author);
      try {
        $entityManager->flush();

        return $this->redirectToRoute(
          'app_authors_show',
          ['id' => $author->getId()],
          Response::HTTP_SEE_OTHER
        );
      } catch (ORMException $e) {
        $form->addError(new FormError('Error occured'));
      }
    }

    return $this->render('authors/new.html.twig', [
      'author' => $author,
      'form' => $form,
    ]);
  }

  #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
  #[IsGranted('IS_AUTHENTICATED')]
  public function edit(
    Request $request,
    Author $author,
    EntityManagerInterface $entityManager,
  ): Response {
    $authorOriginal = clone $author;
    $form = $this->createForm(AuthorType::class, $author);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      try {
        $entityManager->flush();

        return $this->redirectToRoute(
          'app_authors_show',
          ['id' => $author->getId()],
          Response::HTTP_SEE_OTHER
        );
      } catch (ORMException $e) {
        $form->addError(new FormError('Error occured'));
      }
    }

    return $this->render('authors/edit.html.twig', [
      'author' => $authorOriginal,
      'form' => $form,
    ]);
  }

  #[Route('/{id}', name: 'delete', methods: ['POST'], requirements: ['id' => '\d+'])]
  #[IsGranted('IS_AUTHENTICATED')]
  public function delete(
    Request $request,
    Author $author,
    EntityManagerInterface $entityManager,
  ): Response {
    if ($this->isCsrfTokenValid(
      'delete' . $author->getId(),
      $request->getPayload()->getString('_token')
    )) {
      $entityManager->remove($author);
      $entityManager->flush();
    }

    return $this->redirectToRoute(
      'app_authors_index',
      [],
      Response::HTTP_SEE_OTHER
    );
  }
}
