<?php
namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

// entity
use App\Entity\Post;

class SpotService 
{
	/**
	* @var LoggerInterface	
	**/
	private $logger;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ValidatorInterface
     */
    private $validator;

	public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager, ValidatorInterface $validator)
	{
		$this->logger        = $logger;
		$this->entityManager = $entityManager;
		$this->validator     = $validator;
	}	

	/* update spot views count + 1 */
	public function setIsViewed($spot)
	{
		$spot->setViews($spot->getViews() + 1);
		$this->entityManager->flush();
	}

	/* create spot */
	public function store($brand, $model, $city, $country, $images, $user)
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

	/* delete spot */
	public function destroy($spot)
	{
   		$this->entityManager->remove($spot);
   		$this->entityManager->flush();
	}

}