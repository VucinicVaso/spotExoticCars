<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

// entity, repository
use App\Entity\News;
use App\Repository\NewsRepository;
use App\Repository\PostRepository;

class PagesController extends AbstractController
{

    /**
     * @var newsRepository
     */   
    private $newsRepository;
    /**
     * @var PostRepository
     */
    private $postRepository;

    public function __construct(NewsRepository $newsRepository, PostRepository $postRepository)
    {
        $this->newsRepository = $newsRepository;
        $this->postRepository = $postRepository;
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
    public function contact()
    {
        return $this->render('pages/contact.html.twig', [
            "title" => 'Contact SpotExotics'
        ]);
    }
}
