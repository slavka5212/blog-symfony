<?php

namespace App\Repository;

use App\Entity\BlogPost;
use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BlogPost>
 *
 * @method BlogPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogPost[]    findAll()
 * @method BlogPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogPost::class);
    }

    public function add(BlogPost $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BlogPost $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param int $blog_post_id
     * @return Comment[] Returns an array of Comment objects
     */
    public function getComments(int $blog_post_id) : array
    {
        $comment_repository = $this->getEntityManager()->getRepository(Comment::class);

        return $comment_repository->createQueryBuilder('c')
            ->andWhere('c.blog_post_id = :val')
            ->setParameter('val', $blog_post_id)
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return BlogPost[] Returns an array of BlogPost objects
     */
    public function findWithCommentsCount() : array
    {
        $query = $this->createQueryBuilder('b')
            ->leftJoin('App:Comment', 'c',  Join::WITH, 'c.blog_post_id = b.id')
            ->addSelect('COUNT(c.id) AS comments_count')
            ->groupBy('b.id')
            ->orderBy('b.date', 'DESC')
            ->getQuery();

        return $query->getResult();
    }
}
