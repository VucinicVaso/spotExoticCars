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
use App\Entity\Comment;
use App\Repository\CommentRepository;

class AdminCommentsController extends AbstractController 
{
    /**
     * @var CommentRepository
     */
    private $commentRepository;
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

	public function __construct(CommentRepository $commentRepository, EntityManagerInterface $entityManager, RouterInterface $router, FlashBagInterface $flashBag)
	{
		$this->commentRepository = $commentRepository;
		$this->entityManager     = $entityManager;
        $this->router            = $router;
        $this->flashBag          = $flashBag;
	}

    /**
     * @Route("/admin-comments", name="admin_comments")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index()
    {
        return $this->render("admin-comments/index.html.twig", [
            'title'    => "Admin - Comments",
            'comments' => $this->commentRepository->findBy([], ['created_at' => 'DESC'])
        ]);
    }

    /**
     * @Route("admin-comments/delete/{id}", name="admin_comments_delete")
     * @Security("is_granted('ROLE_ADMIN')")
    */
    public function destroy(Comment $comment)
    {
        $this->entityManager->remove($comment);
        $this->entityManager->flush();

        $this->flashBag->add('notice', 'Comment deleted successfully!');
        return new RedirectResponse($this->router->generate('admin_comments'));
    }

}