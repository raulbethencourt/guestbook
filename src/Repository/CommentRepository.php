<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Conference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public const COMMENTS_PER_PAGE = 2;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @return Paginator
     */
    public function getCommentPaginator(Conference $conference, int $offset): Paginator
    {
        $query = $this
            ->createQueryBuilder('c')
            ->andWhere('c.conference = :conference')
            ->andWhere('c.state = :state')
            ->setParameter('conference', $conference)
            ->setParameter('state', 'published')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults(self::COMMENTS_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($query);
    }

    /** @return Comment[] Returns an array of Comment objects */
    // public function findByEmail($email): array
    // {
    //     return $this
    //         ->createQueryBuilder('c')
    //         ->andWhere('c.email = :val')
    //         ->setParameter('val', $email)
    //         ->orderBy('c.id', 'ASC')
    //         ->setMaxResults(10)
    //         ->getQuery()
    //         ->getResult();
    // }

    /**
     * @return null|Comment Returns an array of Comment objects
     * @param mixed $email
     */
    public function findOneByEmail($email): ?Comment
    {
        return $this
            ->createQueryBuilder('c')
            ->andWhere('c.email = :val')
            ->setParameter('val', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
