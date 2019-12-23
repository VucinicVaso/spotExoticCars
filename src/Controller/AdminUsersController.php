<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Doctrine\ORM\EntityManagerInterface;

// entity, repository, form
use App\Entity\User;
use App\Repository\UserRepository;

class AdminUsersController extends AbstractController
{

    /**
     * @var UserRepository
     */   
    private $userRepository;
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

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager, RouterInterface $router, FlashBagInterface $flashBag)
    {
        $this->userRepository = $userRepository;
        $this->entityManager  = $entityManager;
        $this->router         = $router;
        $this->flashBag       = $flashBag;
    }

    /**
     * @Route("/admin-users", name="admin_users")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index()
    {
        return $this->render('admin-users/index.html.twig', [
            'title' => 'Users',
            'users' => $this->userRepository->getUsers()
        ]);
    }

    /**
     * @Route("/admin-users/{id}", name="admin_users_show")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function show(User $user)
    {
        return $this->render('admin-users/show.html.twig', [
            'title' => 'Admin - User: '.$user->getFirstname()." ".$user->getLastname(),
            'user'  => $user
        ]);
    }

    /**
     * @Route("/admin-users/delete/{id}", name="admin_users_delete")
     * @Security("is_granted('ROLE_ADMIN')")
    */
    public function delete(User $user)
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $this->flashBag->add('notice', 'User was deleted');

        return new RedirectResponse($this->router->generate('admin_users'));
    }

}
