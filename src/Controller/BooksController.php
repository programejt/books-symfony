<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BooksRepository;
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

#[Route('/books')]
final class BooksController extends AbstractController
{
  #[Route(name: 'app_books_index', methods: ['GET'])]
  public function index(
    Request $request,
    BooksRepository $booksRepository
  ): Response
  {
    $currentPage = (int) ($request->get('page') ?? 1);
    $searchValue = $request->get('book-title-or-author');

    if ($currentPage < 1) {
      $currentPage = 1;
    }

    $limit = 12;

    $books = $booksRepository->findPaginated($searchValue, $currentPage, $limit);

    return $this->render('books/index.html.twig', [
      'searchValue' => $searchValue,
      'currentPage' => $currentPage,
      'pagesCount' => ceil($books->count() / $limit),
      'books' => $books,
    ]);
  }

  #[Route('/{id}', name: 'app_books_show', methods: ['GET'])]
  public function show(Book $book): Response
  {
    return $this->render('books/show.html.twig', [
        'book' => $book,
    ]);
  }

  #[Route('/new', name: 'app_books_new', methods: ['GET', 'POST'])]
  public function new(
    Request $request,
    EntityManagerInterface $entityManager,
    BookAddEventListener $listener
  ): Response
  {
    $book = new Book();
    $form = $this->createForm(BookType::class, $book);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->persist($book);

      $result = $this->store($form, $book, null, $entityManager);

      if ($result) {
        // $listener = $this->container->get(BookAddEventListener::class);
        $dispatcher = new EventDispatcher();

        $dispatcher->addListener(BookAddEvent::NAME, [$listener, 'onBookAdd']);
        $dispatcher->dispatch(new BookAddEvent($book), BookAddEvent::NAME);
      }

      return $this->redirectToRoute(
        'app_books_show',
        ['id' => $book->getId()],
        Response::HTTP_SEE_OTHER
      );
    }

    return $this->render('books/new.html.twig', [
      'book' => $book,
      'form' => $form,
    ]);
  }

  #[Route('/{id}/edit', name: 'app_books_edit', methods: ['GET', 'POST'])]
  public function edit(
    Request $request,
    Book $book,
    EntityManagerInterface $entityManager
  ): Response
  {
    $bookOriginal = clone $book;
    $form = $this->createForm(BookType::class, $book);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->store($form, $book, $bookOriginal, $entityManager);

      return $this->redirectToRoute(
        'app_books_show',
        ['id' => $book->getId()],
        Response::HTTP_SEE_OTHER
      );
    }

    return $this->render('books/edit.html.twig', [
      'book' => $bookOriginal,
      'form' => $form,
    ]);
  }

  #[Route('/{id}', name: 'app_books_delete', methods: ['POST'])]
  public function delete(
    Request $request,
    Book $book,
    EntityManagerInterface $entityManager
  ): Response
  {
    if ($this->isCsrfTokenValid(
      'delete'.$book->getId(),
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
    FormInterface &$form,
    Book &$book,
    ?Book &$bookOriginal,
    EntityManagerInterface &$entityManager
  ): bool {
    $newPhoto = $form->get('photo')->getData();
    $deletePhoto = $form->has('deletePhoto') ? $form->get('deletePhoto')->getData() : false;
    $photo = $bookOriginal?->getPhoto();

    $book->setPhoto(match(true) {
      $deletePhoto => null,
      $newPhoto != null => 'book'.'-'.bin2hex(random_bytes(13)).'.'.$newPhoto->guessExtension(),
      default => $photo
    });

    try {
      $entityManager->flush();
    } catch (ORMException $e) {
      return false;
    }

    $photoDir = $book->getSystemPhotosDir();

    if ($photo && ($deletePhoto || $newPhoto)) {
      $photo = str_replace(['/', '..', '\\'], '', $photo);
      $photoPath = str_replace('..', '', $photoDir)."/$photo";

      $this->deletePhoto($photoPath);
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

  private function deletePhoto(string $photoFullPath): bool {
    if (file_exists($photoFullPath) && is_file($photoFullPath)) {
      return unlink($photoFullPath);
    }
    return false;
  }
}
