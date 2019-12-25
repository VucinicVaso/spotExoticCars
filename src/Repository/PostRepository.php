<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager, 
    ValidatorInterface $validator)
    {
        parent::__construct($registry, Post::class);
        $this->entityManager   = $entityManager;
        $this->validator       = $validator;
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

    /* get spots by user id */
    public function spotsByUserId($user): array
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

    /* set, validate and create post */
    public function validateAndCreatePost($brand, $model, $city, $country, $images, $user)
    {
        $post = new Post();
        $post->setUser($user);
        $post->setBrand($brand);
        $post->setModel($model);
        $post->setCity($city);
        $post->setCountry($country);
        $post->setCreatedAt(new \DateTime());
        $post->setImages($images);
        
        $errors = $this->validator->validate($post);
        if(count($errors) != 0) {
            $formErrors = [];
            foreach($errors as $error){
                $formErrors[] = $error->getMessage();
            }
            return $formErrors;
        }else {
            $this->entityManager->persist($post);
            $this->entityManager->flush();
            if(empty($post->getId())){ return ['Error! Please try again.']; }
        } 
    }

}
