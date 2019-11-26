<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;

//entity, repository, type
use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\RegisterType;

class SecurityController extends AbstractController
{

    /**
     * @var UserRepository
     */   
    private $userRepository;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
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

    public function __construct(UserRepository $userRepository, FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager, RouterInterface $router, FlashBagInterface $flashBag)
    {
        $this->userRepository = $userRepository;
        $this->formFactory    = $formFactory;
        $this->entityManager  = $entityManager;
        $this->router         = $router;
        $this->flashBag       = $flashBag;
    }

    /**
     * @Route("/register", name="security_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->formFactory->create(RegisterType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user->setUsername();
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setProfileImage('profile.png');
            $user->setCreatedAt(new \DateTime());
            $user->setRoles([User::ROLE_USER]);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->flashBag->add('notice', 'Profile createad successfully!');
            return new RedirectResponse($this->router->generate('security_register'));
        }

        return $this->render('security/register.html.twig', [
            'title' => 'Register',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $AuthenticationUtils)
    {
        return $this->render('security/login.html.twig', [
            'title'      => "Login",
            'last_email' => $AuthenticationUtils->getLastUsername(),
            'error'      => $AuthenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout() {}

}
