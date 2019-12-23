<?php

namespace App\Repository;

use App\Entity\Brand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @method Brand|null find($id, $lockMode = null, $lockVersion = null)
 * @method Brand|null findOneBy(array $criteria, array $orderBy = null)
 * @method Brand[]    findAll()
 * @method Brand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BrandRepository extends ServiceEntityRepository
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
        parent::__construct($registry, Brand::class);
        $this->entityManager   = $entityManager;
        $this->validator       = $validator;
    }

    /* count number of brands */
    public function countBrand()
    {
        return $this->createQueryBuilder('b')
            ->select('count(b.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /* set and create new brand */
    public function setAndCreateBrand($title, $user)
    {
        $brand = new Brand();
        $brand->setTitle($title);
        $brand->setUser($user);
        $brand->setCreatedAt(new \DateTime());
    
        $errors = $this->validator->validate($brand);
        if(count($errors) != 0) {
            $formErrors = [];
            foreach($errors as $error){
                $formErrors[] = $error->getMessage();
            }
            return $formErrors;
        }else {
            $this->entityManager->persist($brand);
            $this->entityManager->flush();
            if(empty($brand->getId())){ return ['Error! Please try again.']; }
        }
    }

    /* set,validate and update brand */
    public function setAndUpdateBrand($brand, $title)
    {
        $brand->setTitle($title);
        $brand->setCreatedAt(new \DateTime());
     
        $errors = $this->validator->validate($brand);
        if(count($errors) != 0) {
            $formErrors = [];
            foreach($errors as $error){
                $formErrors[] = $error->getMessage();
            }
            return $formErrors;
        }else {
            $this->entityManager->flush();
        }
    }

}
