<?php

namespace App\Controller\Admin;

use App\Repository\CommentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_ADMIN_COMMENT")]
class CommentsController extends AbstractController
{
    #[Route('/admin/comments', name: 'app_admin_comments')]
    public function index(Request $request, CommentRepository $commentRepository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $commentRepository->findAllWithSearchQuery($request->query->get('q'), $request->query->has('showDeleted')),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/comments/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
