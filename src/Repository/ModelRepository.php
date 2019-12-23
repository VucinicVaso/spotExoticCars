<?php

namespace App\Repository;

use App\Entity\Model;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @method Model|null find($id, $lockMode = null, $lockVersion = null)
 * @method Model|null findOneBy(array $criteria, array $orderBy = null)
 * @method Model[]    findAll()
 * @method Model[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModelRepository extends ServiceEntityRepository
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
        parent::__construct($registry, Model::class);
        $this->entityManager   = $entityManager;
        $this->validator       = $validator;
    }

    /* count number of models */
    public function countModel()
    {
        return $this->createQueryBuilder('m')
            ->select('count(m.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /* find models by brand id */
    public function findModelsByBrand($id)
    {
        return $this->createQueryBuilder('m')
            ->select('m.id, m.title, b.id AS brand_id')
            ->leftJoin('App\Entity\Brand', 'b', 'WITH', 'm.brand = b.id')
            ->andWhere('m.brand = :brand')
            ->setParameter('brand', $id)
            ->getQuery()
            ->getResult();        
    }

    /* set, validate and create new model */
    public function setAndCreateModel($title, $brand, $user)
    {
        $model = new Model();
        $model->setTitle($title);
        $model->setUser($user);
        $model->setBrand($brand);
        $model->setCreatedAt(new \DateTime());
     
        $errors = $this->validator->validate($model);
        if(count($errors) != 0) {
            $formErrors = [];
            foreach($errors as $error){
                $formErrors[] = $error->getMessage();
            }
            return $formErrors;
        }else {
            $this->entityManager->persist($model);
            $this->entityManager->flush();
            if(empty($model->getId())){ return ['Error! Please try again.']; }
        } 
    }

    /* set, validate and update model */
    public function setAndUpdateModel($model, $brand, $title)
    {
        $model->setTitle($title);
        $model->setBrand($brand);
        $model->setCreatedAt(new \DateTime());
        
        $errors = $this->validator->validate($model);
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
