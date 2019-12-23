<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /* count users */
    public function countUsers()
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->andWhere('u.roles != :role')
            ->setParameter('role', ['ROLE_ADMIN'])
            ->getQuery()
            ->getSingleScalarResult();
    }

    /* get users */
    public function getUsers()
    {
        return $this->createQueryBuilder('u')
            ->select('u.id, u.firstname, u.lastname, u.email, u.profile_image, u.created_at')
            ->andWhere('u.roles != :role')
            ->setParameter('role', ['ROLE_ADMIN'])
            ->getQuery()
            ->getResult();
    }

}
