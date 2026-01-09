<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Video>
 */
class VideoRepository extends ServiceEntityRepository
{

    public PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Video::class);
        $this->paginator = $paginator;
    }

    public function findAllPaginated(int $page)
    {
        $dbQuery = $this->createQueryBuilder('v')->getQuery();
        $pagination = $this->paginator->paginate($dbQuery, $page);

        return $pagination;
    }

    public function findByChildIds(array $ids, int $page)
    {
        $dbQuery = $this->createQueryBuilder('v')
            ->andWhere('v.category IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery();
        $pagination = $this->paginator->paginate($dbQuery, $page);

        return $pagination;
    }

}
