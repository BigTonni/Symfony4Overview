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
        return new Response('List of articles.');
    }

    /**
     * @Route("/blog/{page}")
     */
    public function show(){
        return new Response('Current article.');
    }

}
