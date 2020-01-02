<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

// entity, repository, form
use App\Entity\Contact;
use App\Repository\ContactRepository;

class ContactController extends AbstractController
{
	/**
     * @var ContactRepository
     */
    private $contactRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var RouterInterface
     */
    private $router; 
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(ContactRepository $contactRepository, EntityManagerInterface $entityManager, RouterInterface $router, FlashBagInterface $flashBag)
    {
       	$this->contactRepository = $contactRepository;
        $this->entityManager     = $entityManager;
        $this->router            = $router;
        $this->flashBag          = $flashBag;
    }

    /**
     * @Route("/messages", name="messages")
     * @Security("is_granted('ROLE_ADMIN')")
    */
    public function index()
    {
        return $this->render('contact/index.html.twig', [
            'title'    => 'Messages',
            'messages' => $this->contactRepository->findBy([], ['created_at' => 'DESC'])
        ]);
    }

    /**
     * @Route("/messages/delete/{id}", name="messages_delete")
     * @Security("is_granted('ROLE_ADMIN')")
    */
    public function delete(Contact $contact)
    {
        $this->entityManager->remove($contact);
        $this->entityManager->flush();

        $this->flashBag->add('notice', 'Message was deleted');
        return new RedirectResponse($this->router->generate('messages'));
    }

}
