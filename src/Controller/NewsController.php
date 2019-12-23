<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;

//entity, repository, type
use App\Entity\News;
use App\Repository\NewsRepository;
use App\Form\NewsType;

class NewsController extends AbstractController
{

    /**
     * @var newsRepository
     */   
    private $newsRepository;
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

    public function __construct(NewsRepository $newsRepository, FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager, RouterInterface $router, FlashBagInterface $flashBag)
    {
        $this->newsRepository = $newsRepository;
        $this->formFactory    = $formFactory;
        $this->entityManager  = $entityManager;
        $this->router         = $router;
        $this->flashBag       = $flashBag;
    }

    /**
     * @Route("/news", name="news")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index(Request $request)
    {
        $article = new News();
        $form = $this->formFactory->create(NewsType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
        	/* upload images */
            $files = $form->get('images')->getData();
            foreach($files as $file){
                $filename = "article-".uniqid()."-".str_replace(" ", "-", $article->getTitle()).".".$file->guessExtension();
                $file->move($this->getParameter('img_directory'), $filename);
                $imgArr[] = $filename;
            }
            $article->setImages($imgArr);      	
            $article->setCreatedAt(new \DateTime());
            $article->setUser($this->getUser());

            $this->entityManager->persist($article);
            $this->entityManager->flush();

            $this->flashBag->add('notice', 'Article created successfully!');
            return new RedirectResponse($this->router->generate('news'));
        }

        return $this->render('news/index.html.twig', [
            'title' => 'News',
            'news'  => $this->newsRepository->findBy([], ['created_at' => 'DESC']),
            'form'  => $form->createView()
        ]);
    }

    /**
     * @Route("/article/{id}", name="news_show")
     */
    public function show(News $article)
    {
        return $this->render('news/show.html.twig', [
            'title'   => $article->getTitle(),
            'article' => $article,
        ]);
    }

    /**
    * @Route("/news/delete/{id}", name="news_delete")
    * @Security("is_granted('ROLE_ADMIN')")
    */
    public function delete(News $article)
    {
        $this->entityManager->remove($article);
        $this->entityManager->flush();
        $this->flashBag->add('notice', 'Article was deleted');
        return new RedirectResponse($this->router->generate('news'));      
    }    

}
