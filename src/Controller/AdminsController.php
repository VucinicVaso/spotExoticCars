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
use App\Repository\NewsRepository;
use App\Repository\BrandRepository;
use App\Repository\ModelRepository;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;

class AdminsController extends AbstractController
{

    /**
     * @var UserRepository
     */   
    private $userRepository;
    /**
     * @var NewsRepository
     */
    private $newsRepository;
    /**
     * @var BrandRepository
     */
    private $brandRepository;
    /**
     * @var ModelRepository
     */
    private $modelRepository;
    /**
     * @var PostRepository
     */
    private $postRepository;
    /**
     * @var CommentRepository
     */
    private $commentRepository;  
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

    public function __construct(UserRepository $userRepository, NewsRepository $newsRepository, BrandRepository $brandRepository, ModelRepository $modelRepository, PostRepository $postRepository, CommentRepository $commentRepository,
        FormFactoryInterface $formFactory, EntityManagerInterface $entityManager, RouterInterface $router, FlashBagInterface $flashBag)
    {
        $this->userRepository    = $userRepository;
        $this->newsRepository    = $newsRepository;
        $this->brandRepository   = $brandRepository;
        $this->modelRepository   = $modelRepository;
        $this->postRepository    = $postRepository;
        $this->commentRepository = $commentRepository;
        $this->formFactory       = $formFactory;
        $this->entityManager     = $entityManager;
        $this->router            = $router;
        $this->flashBag          = $flashBag;
    }

    /**
     * @Route("/admin", name="admin_profile")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index()
    {
    	$user = $this->getUser();

        return $this->render('admins/index.html.twig', [
            'title'    => 'Admin: '.$user->getFirstname()." ".$user->getLastname(),
            'user'     => $user,
            'news'     => $this->newsRepository->countNews(),
            'users'    => $this->userRepository->countUsers($user->getId()),
            'posts'    => $this->postRepository->countPosts(),
            'comments' => $this->commentRepository->countComments(),
            'brand'    => $this->brandRepository->countBrand(),
            'model'    => $this->modelRepository->countModel()
        ]);
    }

    /**
     * @Route("/admin/edit", name="admin_edit_profile")
     * @Security("is_granted('ROLE_ADMIN')")
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
            return new RedirectResponse($this->router->generate('admin_edit_profile'));
        }

    	return $this->render('admins/edit.html.twig', [
            'title' => 'Update profile',
            'form'  => $form->createView()
    	]);
    }
    
    /**
     * @Route("/admin/password", name="admin_edit_password")
     * @Security("is_granted('ROLE_ADMIN')")
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
            return new RedirectResponse($this->router->generate('admin_edit_password'));
        }
        return $this->render('admins/password.html.twig', [
            'title' => 'Update password',
            'form'  => $form->createView()
        ]);      
    }

}
