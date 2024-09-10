<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Demontpx\ParsedownBundle\Parsedown;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function homepage(ArticleRepository $repository)
    {
        $articles = $repository->findLatest();

        $banner = rand(0, 1)
            ? 'images/cat-banner.jpg'
            : 'images/cat-banner1.jpg'
        ;

        return $this->render('articles/homepage.html.twig', [
            'articles' => $articles,
            'banner' => $banner,
        ]);
    }

    #[Route('/articles/{slug}', name: 'app_article_show')]
    public function show(Article $article)
    {
        return $this->render('articles/show.html.twig', [
            'article' => $article,
        ]);
    }
}