<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Figure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Figure>
 */
class FigureRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Figure::class);

    }
    public function findAllFigures(): array
    {
        return $this->findBy([], ['createdAt' => 'DESC']);
    }

    public function findFigureById(int $id): ?Figure
    {
        return $this->find($id);
    }

    public function save(Figure $figure): void
    {
        $this->entityManager->persist($figure);
        $this->entityManager->flush();
    }

    public function findFigureWithComments(int $id): ?Figure
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.comments', 'c')
            ->addSelect('c')
            ->where('f.id = :id')
            ->setParameter('id', $id)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getCommentsQueryForFigure(Figure $figure): Query
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('c', 'u')
            ->from(Comment::class, 'c')
            ->join('c.author', 'u')
            ->where('c.figure = :figure')
            ->setParameter('figure', $figure)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery();
    }

    public function getPaginatedComments(Figure $figure, int $page, PaginatorInterface $paginator): PaginationInterface
    {
        $query = $this->createQueryBuilder('c')
            ->andWhere('c.figure = :figure')
            ->setParameter('figure', $figure)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery();

        return $paginator->paginate($query, $page, 10);
    }



    //    /**
    //     * @return Figure[] Returns an array of Figure objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Figure
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
