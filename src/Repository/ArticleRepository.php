<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Monolog\DateTimeImmutable;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findLatestPublished(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.publishedAt IS NOT NULL')
            ->orderBy('a.publishedAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findLatest(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.publishedAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findPublished(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.publishedAt IS NOT NULL')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllPublishedLastWeek()
    {
        return $this->published($this->latest())
            ->andWhere('a.publishedAt >= :week_ago')
            ->setParameter('week_ago', new DateTimeImmutable('-1 week'))
            ->getQuery()
            ->getResult()
            ;
    }

    private function published(QueryBuilder $qb = null): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder($qb)->andWhere('a.publishedAt IS NOT NULL');
    }

    public function latest(QueryBuilder $qb = null): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder($qb)->orderBy('a.publishedAt', 'DESC');
    }

    /**
     * @param QueryBuilder|null $qb
     * @return QueryBuilder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $qb = null): QueryBuilder
    {
        return $qb ?? $this->createQueryBuilder('a');
    }
}
