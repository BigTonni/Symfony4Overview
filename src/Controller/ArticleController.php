<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="article")
     */
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }
    
    /**
     * @Route("/blog/list")
     */
    public function bloglist(): Response
    {
        $articles = [
            ['title' => 'First', 'slug' => '1', 'authorName' => 'Author1', 'body' => 'Desc1'],
            ['title' => 'Second', 'slug' => '2', 'authorName' => 'Author2', 'body' => 'Desc2'],
            ['title' => 'Third', 'slug' => '3', 'authorName' => 'Author3', 'body' => 'Desc3'],
        ];
        return $this->render('article/list.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/blog/{slug}")
     */
    public function show($article): Response
    {
        return $this->render('article/article_details.html.twig', [
            'article' => $article,
        ]);
    }

}
