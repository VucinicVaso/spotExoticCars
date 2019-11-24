<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;

//entity, repository, type
use App\Entity\User;
use App\Form\ProfileType;
use App\Form\ProfilePasswordType;
use App\Repository\UserRepository;

class ProfileController extends AbstractController
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
     * @Route("/profile", name="profile")
     * @Security("is_granted('ROLE_USER')")
     */
    public function index()
    {
        $user = $this->getUser();

        return $this->render('profile/index.html.twig', [
            'title' => 'Profile: '.$user->getFirstname()." ".$user->getLastname(),
            'user'  => $user,
            'spots' => []
        ]);
    }

    /**
     * @Route("/edit", name="profile_edit")
     * @Security("is_granted('ROLE_USER')")
    */
    public function edit(Request $request)
    {
        $user = $this->getUser();
        $form = $this->formFactory->create(ProfileType::class, $user);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
        	$user->setUsername();
            /* upload image */
            $file = $form->get('profile_image')->getData();
            if(!empty($file)){
                $filename = md5(uniqid()).'.'.$file->guessExtension();
                $image    = $file->move($this->getParameter('img_directory'), $filename);
                $user->setProfileImage($filename);
            }else {
                $user->setProfileImage($user->getProfileImage());
            }
            
            $this->entityManager->flush();

            $this->flashBag->add('notice', 'Profile updated successfully!');
            return new RedirectResponse($this->router->generate('profile_edit'));
        }

    	return $this->render('profile/edit.html.twig', [
            'title' => 'Update profile',
            'form'  => $form->createView()
    	]);
    }

    /**
     * @Route("/password", name="profile_edit_password")
     * @Security("is_granted('ROLE_USER')")
    */
    public function password(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();
        $form = $this->formFactory->create(ProfilePasswordType::class, $user);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
        
            $this->entityManager->flush();

            $this->flashBag->add('notice', 'Profile updated successfully!');
            return new RedirectResponse($this->router->generate('profile_edit_password'));
        }
        return $this->render('profile/password.html.twig', [
            'title' => 'Update password',
            'form'  => $form->createView()
        ]);      
    }

}
