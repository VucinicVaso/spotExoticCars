<?php
namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

class SpotViewed
{
	/**
	* @var LoggerInterface	
	**/
	private $logger;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
	
	public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager)
	{
		$this->logger        = $logger;
		$this->entityManager = $entityManager;
	}	

	public function setIsViewed($spot)
	{
		$spot->setViews($spot->getViews() + 1);
		$this->entityManager->flush();

		$this->logger->info(true);
	}
}