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
    public function index()
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }
    
    /**
     * @Route("/blog/list")
     */
    public function bloglist(){
        $articles = [
            ['title' => 'First', 'authorName' => 'Author1', 'body' => 'Desc1'],
            ['title' => 'Second', 'authorName' => 'Author2', 'body' => 'Desc2'],
            ['title' => 'Third', 'authorName' => 'Author3', 'body' => 'Desc3'],
        ];
        return $this->render('article/list.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/blog/{page}")
     */
    public function show(){
        return new Response('Current article.');
    }

}
