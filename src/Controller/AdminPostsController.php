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

// entity, repository, form
use App\Entity\Post;
use App\Repository\PostRepository;

class AdminPostsController extends AbstractController
{
    /**
     * @var PostRepository
     */
    private $postRepository;
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

	public function __construct(PostRepository $postRepository, EntityManagerInterface $entityManager, RouterInterface $router, FlashBagInterface $flashBag)
	{
		$this->postRepository = $postRepository;
		$this->entityManager  = $entityManager;
        $this->router         = $router;
        $this->flashBag       = $flashBag;
	}

    /**
     * @Route("/admin-spots", name="admin_spots")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index()
    {
        return $this->render("admin-spots/index.html.twig", [
            'title' => "Admin - Spots",
            'spots' => $this->postRepository->findBy([], ['created_at' => 'DESC'])
        ]);
    }

    /**
     * @Route("/admin-spots/{id}", name="admin_spots_show")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function show(Post $post)
    {
        return $this->render("admin-spots/show.html.twig", [
            'title' => "Admin - Spot: ".$post->getId(),
            'spot'  => $post,
        ]);
    }

    /**
     * @Route("admin-spots/delete/{id}", name="admin_spots_delete")
     * @Security("is_granted('ROLE_ADMIN')")
    */
    public function destroy(Post $post)
    {
        $this->entityManager->remove($post);
        $this->entityManager->flush();

        $this->flashBag->add('notice', 'Spot deleted successfully!');
        return new RedirectResponse($this->router->generate('admin_spots'));
    }

}
