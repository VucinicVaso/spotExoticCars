<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;

// entity, repository, form, services
use App\Entity\Post;
use App\Entity\Comment;
use App\Repository\PostRepository;
use App\Repository\BrandRepository;
use App\Repository\ModelRepository;
use App\Form\CommentType;
use App\Service\SpotService;

class PostsController extends AbstractController
{
    /**
     * @var PostRepository
     */
    private $postRepository;
	/**
     * @var BrandRepository
     */
    private $brandRepository;
	/**
     * @var ModelRepository
     */
    private $modelRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var RouterInterface
     */
    private $router; 
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

	public function __construct(PostRepository $postRepository, BrandRepository $brandRepository, ModelRepository $modelRepository, EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, RouterInterface $router, FlashBagInterface $flashBag)
	{
		$this->postRepository  = $postRepository;
		$this->brandRepository = $brandRepository;
		$this->modelRepository = $modelRepository;
		$this->entityManager   = $entityManager;
        $this->formFactory     = $formFactory;
        $this->router          = $router;
        $this->flashBag        = $flashBag;
    }

    /**
     * @Route("/spots", name="spots")
     */
    public function index()
    {
        return $this->render("spots/index.html.twig", [
            'title' => "Spots",
            'spots' => $this->postRepository->findBy([], ['created_at' => 'DESC'])
        ]);
    }

    /**
     * @Route("/spots/{id}", name="spots_show", requirements={"id":"\d+"})
     */
    public function show(Post $post, SpotService $spotService, Request $request)
    {
        $spotService->setIsViewed($post); // service: set spot is viewed

        $comment = new Comment();
        $form = $this->formFactory->create(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setPost($post);
            $comment->setCreatedAt(new \DateTime());

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $this->flashBag->add('notice', 'Comment createad successfully!');
            return new RedirectResponse($this->router->generate('spots_show', ['id' => $post->getId()] ));
        }

        return $this->render("spots/show.html.twig", [
            'title'         => "Spot: ".$post->getBrand()->getTitle()." ".$post->getModel()->getTitle(),
            'spot'          => $post,
            'simular_spots' => $this->postRepository->findBy(['brand' => $post->getBrand(), 'model' => $post->getModel()], ['created_at' => 'DESC']),
            'form'          => $form->createView()
        ]);
    }

    /**
     * @Route("/search", name="search", methods={"POST"})
     */
    public function search(Request $request)
    {
        return $this->render('spots/search.html.twig', [
            'title' => 'Search',
            'spots' => $this->postRepository->findBy([
                'brand' => $this->brandRepository->find($request->get('search_brand')),
                'model' => $this->modelRepository->find($request->get('search_model'))
                ], ['created_at' => 'DESC'])
        ]);
    }

    /**
     * @Route("spots/delete/{id}", name="spots_delete", requirements={"id":"\d+"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
    */
    public function destroy(SpotService $spotService, Post $post)
    {
        if(!empty($post)){
            $spotService->destroy($post);
            $this->flashBag->add('notice', 'Spot deleted successfully!');
        }else {
            $this->flashBag->add('notice', 'Error! Something went wrong. Please try again.');
        }

        return new RedirectResponse($this->router->generate('profile'));
    }

    /**
     * @Route("/spots/create", name="spots_create", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function create(SpotService $spotService, Request $request)
    {
        $files = $request->files->get('images');
        $imgArr = [];
        if(!empty($files)){
            foreach($files as $file){
                $filename = md5(uniqid()).'.'.$file->guessExtension();
                $file->move($this->getParameter('img_directory'), $filename);
                $imgArr[] = $filename;
            }
        }

        $brand = $this->brandRepository->find($request->get('brand'));
        $model = $this->modelRepository->find($request->get('model'));

        $validationErrors = $spotService->store($brand, $model, $request->get('city'), $request->get('country'), $imgArr, $this->getUser());

        return !empty(($validationErrors))
            ? $this->json(['errors' => $validationErrors], 400)
            : $this->json(['success' => 'Spot created successfully!'], 200);
    }

}
