<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

class BookRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Book::class);
  }

  public function findPaginated(
    ?string $titleOrAuthor = null,
    int $currentPage = 1,
    int $limit = 10,
  ): Paginator {
    $query = $this
      ->createQueryBuilder('b');

    if ($titleOrAuthor) {
      $expression = $this->getEntityManager()->getExpressionBuilder();

      $query
        ->innerJoin('b.authors', 'a')
        ->where(
          $expression->orX(
            $expression->like(
              'LOWER(b.title)',
              ':searchValue',
            ),
            $expression->like(
              'CONCAT(LOWER(a.name), \' \', LOWER(a.surname))',
              ':searchValue',
            ),
          )
        )
        ->setParameter('searchValue', "%" . strtolower($titleOrAuthor) . "%");
    }

    $query->orderBy('b.id', 'DESC');

    $paginator = new Paginator($query, true);

    $paginator
      ->getQuery()
      ->setFirstResult(($currentPage - 1) * $limit)
      ->setMaxResults($limit);

    return $paginator;
  }

  //    /**
  //     * @return Books[] Returns an array of Books objects
  //     */
  //    public function findByExampleField($value): array
  //    {
  //        return $this->createQueryBuilder('b')
  //            ->andWhere('b.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->orderBy('b.id', 'ASC')
  //            ->setMaxResults(10)
  //            ->getQuery()
  //            ->getResult()
  //        ;
  //    }

  //    public function findOneBySomeField($value): ?Books
  //    {
  //        return $this->createQueryBuilder('b')
  //            ->andWhere('b.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
