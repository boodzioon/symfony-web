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

    public function findByChildIds(array $ids, int $page, ?string $sortMethod)
    {
        $sortMethod = $sortMethod != 'rating' ? $sortMethod : 'ASC';

        $dbQuery = $this->createQueryBuilder('v')
            ->andWhere('v.category IN (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy('v.title', $sortMethod)
            ->getQuery();

        return $this->paginator->paginate($dbQuery, $page);
    }

    public function findByTitle(string $query, int $page, ?string $sortMethod)
    {
        $sortMethod = $sortMethod != 'rating' ? $sortMethod : 'ASC';

        $queryBuilder = $this->createQueryBuilder('v');
        $searchTerms = $this->prepareQuery($query);

        foreach ($searchTerms as $key => $term) {
            $queryBuilder
                ->orWhere('lower(v.title) LIKE lower(:t_' . $key . ')')
                ->setParameter('t_' . $key, '%' . trim($term) . '%');
        }

        $dbQuery = $queryBuilder
            ->orderBy('v.title', $sortMethod)
            ->getQuery();

        return $this->paginator->paginate($dbQuery, $page);
    }

    private function prepareQuery(string $query)
    {
        return explode(' ', $query);
    }

}
