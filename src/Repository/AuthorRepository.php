<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function findPaginated(
      ?string $name = null,
      int $currentPage = 1,
      int $limit = 10,
    ): Paginator {
      $query = $this->createQueryBuilder('a');

      if ($name) {
        $expression = $this->getEntityManager()->getExpressionBuilder();

        $query
          ->where(
            $expression->like(
              'CONCAT(LOWER(a.name), \' \', LOWER(a.surname))',
              ':name',
            ),
          )
          ->setParameter('name', '%'.strtolower($name).'%');
      }

      $query->orderBy('a.name', 'ASC');

      $paginator = new Paginator($query, true);

      $paginator
        ->getQuery()
        ->setFirstResult(($currentPage - 1) * $limit)
        ->setMaxResults($limit);

      return $paginator;
    }

    //    /**
    //     * @return Author[] Returns an array of Author objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Author
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
