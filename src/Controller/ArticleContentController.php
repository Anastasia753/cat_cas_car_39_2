<?php

namespace App\Controller;


use CatCasCarSymfony\ArticleContentProviderBundle\ArticleContentProvider;
use Demontpx\ParsedownBundle\Parsedown;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleContentController extends AbstractController
{
    /**
     * @Route("/article_content", name="app_article_content")
     */
    public function articleContent(Request $request, ArticleContentProvider $articleContentProvider, Parsedown $parsedown, AdapterInterface $cache)
    {
        $paragraphs = (int)$request->get('paragraphs');
        $word       = $request->get('word');
        $wordsCount = (int)$request->get('wordsCount');

        $articleContent = $articleContentProvider->get($paragraphs, $word, $wordsCount);

        $item = $cache->getItem('markdown_' . md5($articleContent));

        if (! $item->isHit()) {
            $item->set($parsedown->text($articleContent));
            $cache->save($item);
        }

        $articleContent = $item->get();

        return $this->render('articles/content.html.twig', [
            'articleContent' => $articleContent,
        ]);
    }
}
