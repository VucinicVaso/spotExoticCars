<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

// entity, repository
use App\Entity\News;
use App\Repository\NewsRepository;

class PagesController extends AbstractController
{

    /**
     * @var newsRepository
     */   
    private $newsRepository;

    public function __construct(NewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
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
            "count_posts"        => 0,
            "count_posts_by_day" => 0
        ]);
    }

}
