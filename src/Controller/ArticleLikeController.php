<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleLikeController extends AbstractController
{
    #[Route("/articles/{slug}/like/{type<like|dislike>}", name: "app_article_like", methods: ["POST"])]
    public function like(Article $article, $type, LoggerInterface $logger, EntityManagerInterface $em)
    {
        if ($type === 'like') {
            $article->like();
            $logger->info('Кто-то лайкнул статью');
        } else {
            $article->dislike();
            $logger->info('Какая досада, дизлайк');
        }

        $em->flush();

        return $this->json(['likes' => $article->getLikeCount()]);
    }
}