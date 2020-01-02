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
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->andWhere('p.created_at >= :date')
            ->setParameter('date', new \DateTime('- 1 DAY'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    /* get spots for proile */
    public function profileSpots($user): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.id, p.city, p.country, p.created_at, p.images, p.views')
            ->leftJoin('App\Entity\Brand', 'b', 'WITH', 'p.brand = b.id')
            ->addSelect('b.id AS brand_id, b.title AS brand_title')
            ->leftJoin('App\Entity\Model', 'm', 'WITH', 'p.model = m.id')
            ->addSelect('m.id AS model_id, m.title AS model_title')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /* get spots by user id */
    public function spotsByUserId($user): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.id, p.city, p.country, p.created_at, p.images')
            ->leftJoin('App\Entity\Brand', 'b', 'WITH', 'p.brand = b.id')
            ->addSelect('b.id AS brand_id, b.title AS brand_title')
            ->leftJoin('App\Entity\Model', 'm', 'WITH', 'p.model = m.id')
            ->addSelect('m.id AS model_id, m.title AS model_title')
            ->leftJoin('App\Entity\User', 'u', 'WITH', 'p.user = u.id')
            ->addSelect('u.id AS user_id, u.username')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
