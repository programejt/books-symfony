<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use App\Event\BookAddEvent;
use App\EventListener\BookAddEventListener;
use App\Service\FileSystem;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Enum\UserRole;

#[Route('/books', name: 'app_books_')]
final class BooksController extends AbstractController
{
  #[Route(name: 'index', methods: ['GET'])]
  public function index(
    Request $request,
    BookRepository $bookRepository,
  ): Response {
    $currentPage = (int) $request->get('page', 1);
    $searchValue = $request->get('search');

    if ($currentPage < 1) {
      $currentPage = 1;
    }

    $limit = 12;

    $books = $bookRepository->findPaginated($searchValue, $currentPage, $limit);

    return $this->render('books/index.html.twig', [
      'searchValue' => $searchValue,
      'currentPage' => $currentPage,
      'pagesCount' => ceil($books->count() / $limit),
      'books' => $books,
    ]);
  }

  #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
  public function show(Book $book): Response
  {
    return $this->render('books/show.html.twig', [
      'book' => $book,
    ]);
  }

  #[Route('/new', name: 'new', methods: ['GET', 'POST'], priority: 2)]
  #[IsGranted(UserRole::Admin->value)]
  public function new(
    Request $request,
    EntityManagerInterface $entityManager,
    BookAddEventListener $listener,
  ): Response {
    $book = new Book();
    $form = $this->createForm(BookType::class, $book);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->persist($book);

      if ($this->store($form, $book, null, $entityManager)) {
        $dispatcher = new EventDispatcher();

        $dispatcher->addListener(BookAddEvent::NAME, $listener);
        $dispatcher->dispatch(new BookAddEvent($book), BookAddEvent::NAME);

        return $this->redirectToRoute(
          'app_books_show',
          ['id' => $book->getId()],
          Response::HTTP_SEE_OTHER
        );
      }

      $form->addError(new FormError('Error occured'));
    }

    return $this->render('books/new.html.twig', [
      'book' => $book,
      'form' => $form,
    ]);
  }

  #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
  #[IsGranted(UserRole::Admin->value)]
  public function edit(
    Request $request,
    Book $book,
    EntityManagerInterface $entityManager,
  ): Response {
    $bookOriginal = clone $book;
    $form = $this->createForm(BookType::class, $book);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      if ($this->store($form, $book, $bookOriginal, $entityManager)) {
        return $this->redirectToRoute(
          'app_books_show',
          ['id' => $book->getId()],
          Response::HTTP_SEE_OTHER
        );
      }
      $form->addError(new FormError('Error occured'));
    }

    return $this->render('books/edit.html.twig', [
      'book' => $bookOriginal,
      'form' => $form,
    ]);
  }

  #[Route('/{id}', name: 'delete', methods: ['POST'], requirements: ['id' => '\d+'])]
  #[IsGranted(UserRole::Admin->value)]
  public function delete(
    Request $request,
    Book $book,
    EntityManagerInterface $entityManager,
  ): Response {
    if ($this->isCsrfTokenValid(
      'delete' . $book->getId(),
      $request->getPayload()->getString('_token')
    )) {
      $entityManager->remove($book);
      $entityManager->flush();
    }

    return $this->redirectToRoute(
      'app_books_index',
      [],
      Response::HTTP_SEE_OTHER
    );
  }

  private function store(
    FormInterface $form,
    Book $book,
    ?Book $bookOriginal,
    EntityManagerInterface $entityManager,
  ): bool {
    /** @var Symfony\Component\HttpFoundation\File $newPhoto */
    $newPhoto = $form->get('photo')->getData();
    $deletePhoto = $form->has('deletePhoto') ? $form->get('deletePhoto')->getData() : false;
    $photo = $bookOriginal?->getPhoto();

    $book->setPhoto(match (true) {
      $deletePhoto => null,
      $newPhoto !== null => 'book' . '-' . bin2hex(random_bytes(13)) . '.' . $newPhoto->guessExtension(),
      default => $photo
    });

    try {
      $entityManager->flush();
    } catch (ORMException $e) {
      return false;
    }

    $photoDir = FileSystem::getDocumentRoot().$book->getPhotosDir();

    if ($photo && ($deletePhoto || $newPhoto)) {
      FileSystem::deleteFile("$photoDir/$photo");
    }

    if ($newPhoto) {
      try {
        $newPhoto->move($photoDir, $book->getPhoto());
      } catch (FileException $e) {
        return false;
      }
    }

    return true;
  }
}
