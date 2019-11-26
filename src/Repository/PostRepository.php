<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /* count number of posts */
    public function countPosts()
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /* count number of posts by day */
    public function countPostsByDay() 
    {
        //'.new \DateTime("now").' - INTERVAL 1 DAY
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->andWhere('p.created_at >= :date')
            ->setParameter('date', new \DateTime('-1 DAY'))
            ->getQuery()
            ->getSingleScalarResult();
    }

}
