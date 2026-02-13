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
        $queryBuilder = $this->createQueryBuilder('v')->getQuery();
        $pagination = $this->paginator->paginate($queryBuilder, $page);

        return $pagination;
    }

    public function findByChildIds(array $ids, int $page, ?string $sortMethod)
    {
        $queryBuilder = $this->createQueryBuilder('v');

        if ($sortMethod != 'rating') {
            $queryBuilder
                ->leftJoin('v.comments', 'c')
                ->leftJoin('v.usersThatLike', 'l')
                ->leftJoin('v.usersThatDontLike', 'd')
                ->addSelect('c', 'l', 'd')
                ->andWhere('v.category IN (:ids)')
                ->setParameter('ids', $ids)
                ->orderBy('v.title', $sortMethod);
        } else {
            $queryBuilder
                ->addSelect('COUNT(l) AS HIDDEN likes', 'c')
                ->leftJoin('v.usersThatLike', 'l')
                ->leftJoin('v.comments', 'c')
                ->andWhere('v.category IN (:ids)')
                ->setParameter('ids', $ids)
                ->groupBy('v', 'c')
                ->orderBy('likes', 'DESC');
        }
        $q = $queryBuilder->getQuery();

        return $this->paginator->paginate($queryBuilder, $page, Video::perPage);
    }

    public function findByTitle(string $query, int $page, ?string $sortMethod)
    {
        $queryBuilder = $this->createQueryBuilder('v');
        $searchTerms = $this->prepareQuery($query);

        foreach ($searchTerms as $key => $term) {
            $queryBuilder
                ->orWhere('lower(v.title) LIKE lower(:t_' . $key . ')')
                ->setParameter('t_' . $key, '%' . trim($term) . '%');
        }

        if ($sortMethod != 'rating') {
            $queryBuilder
                ->orderBy('v.title', $sortMethod)
                ->leftJoin('v.comments', 'c')
                ->leftJoin('v.usersThatLike', 'l')
                ->leftJoin('v.usersThatDontLike', 'd')
                ->addSelect('c', 'l', 'd')
                ->orderBy('v.title', $sortMethod);
        } else {
            $queryBuilder
                ->addSelect('COUNT(l) AS HIDDEN likes', 'c')
                ->leftJoin('v.usersThatLike', 'l')
                ->leftJoin('v.comments', 'c')
                ->groupBy('v', 'c')
                ->orderBy('likes', 'DESC');
        }

        return $this->paginator->paginate($queryBuilder, $page, Video::perPage);
    }

    public function getVideoDetails(int $id)
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.comments', 'c')
            ->leftJoin('c.author', 'u')
            ->addSelect('c', 'u')
            ->where("v.id = :id")
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function prepareQuery(string $query)
    {
        return explode(' ', $query);
    }

}
