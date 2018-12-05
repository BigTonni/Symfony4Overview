<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="blog_index")
     */
    public function index(): Response
    {
        return $this->render('article/index.html.twig');
    }
    
    /**
     * @Route("/blog/list", name="blog_list")
     */
    public function blogList(): Response
    {
        $articles = [
            ['title' => 'First', 'slug' => '1', 'author' => 'Author1', 'body' => 'Desc1', 'publishedAt' => '04-12-2018'],
            ['title' => 'Second', 'slug' => '2', 'author' => 'Author2', 'body' => 'Desc2', 'publishedAt' => '04-12-2018'],
            ['title' => 'Third', 'slug' => '3', 'author' => 'Author3', 'body' => 'Desc3', 'publishedAt' => '04-12-2018'],
        ];
        return $this->render('article/article_list.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/articles/{slug}", methods={"GET"}, name="blog_article")
     */
    public function articleShow(Article $article): Response
    {
        return $this->render('article/article_show.html.twig', [
            'article' => $article,
        ]);
    }

}
