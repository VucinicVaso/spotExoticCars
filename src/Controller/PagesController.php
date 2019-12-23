<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;

// entity, repository, form
use App\Entity\Contact;
use App\Entity\News;
use App\Repository\NewsRepository;
use App\Repository\PostRepository;
use App\Form\ContactType;

class PagesController extends AbstractController
{

    /**
     * @var NewsRepository
     */   
    private $newsRepository;
    /**
     * @var PostRepository
     */
    private $postRepository;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(NewsRepository $newsRepository, PostRepository $postRepository, FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager, RouterInterface $router, FlashBagInterface $flashBag)
    {
        $this->newsRepository = $newsRepository;
        $this->postRepository = $postRepository;
        $this->formFactory    = $formFactory;
        $this->entityManager  = $entityManager;
        $this->router         = $router;
        $this->flashBag       = $flashBag;
    }

    /**
     * @Route("/", name="pages_index")
     */
    public function index()
    {
        return $this->render('pages/index.html.twig', [
            "title"              => 'SpotExotics',
            "article"            => $this->newsRepository->findOneBy([], ['created_at' => 'DESC']),
            "news"               => $this->newsRepository->findBy([], ['created_at' => 'DESC']),
            "count_posts"        => $this->postRepository->countPosts(),
            "count_posts_by_day" => $this->postRepository->countPostsByDay()
        ]);
    }

    /**
     * @Route("/about", name="pages_about")
     */
    public function about()
    {
        return $this->render('pages/about.html.twig', [
            "title" => 'About SpotExotics'
        ]);
    }

    /**
     * @Route("/becomeaspotter", name="pages_becomeaspotter")
     */
    public function becomeaspotter()
    {
        return $this->render('pages/becomeaspotter.html.twig', [
            "title" => 'Become a Spotter'
        ]);
    }

    /**
     * @Route("/contact", name="pages_contact")
     */
    public function contact(Request $request)
    {
        $contact = new Contact();
        $form = $this->formFactory->create(ContactType::class, $contact);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $contact->setCreatedAt(new \DateTime());

            $this->entityManager->persist($contact);
            $this->entityManager->flush();

            $this->flashBag->add('notice', 'Message createad successfully!');
            return new RedirectResponse($this->router->generate('pages_contact'));
        }

        return $this->render('pages/contact.html.twig', [
            "title" => 'Contact Us',
            'form' => $form->createView()
        ]);
    }

}
