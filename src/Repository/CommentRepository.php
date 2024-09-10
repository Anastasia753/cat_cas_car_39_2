<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findAllWithSearchQuery(?string $search, bool $withSoftDeletes = false)
    {
        $qb = $this->createQueryBuilder('c');

        $qb
            ->innerJoin('c.article', 'a')
            ->addSelect('a')
        ;

        if ($search) {
            $qb
                ->andWhere('c.content LIKE :search OR c.authorName LIKE :search OR a.title LIKE :search')
                ->setParameter('search', "%$search%")
            ;
        }

        if ($withSoftDeletes) {
            $this->getEntityManager()->getFilters()->disable('softdeleteable');
        }

        return $qb
            ->orderBy('c.createdAt', 'DESC')
        ;
    }
}
