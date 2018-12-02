<?php

namespace App\Controller;

//use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController
{
    /**
     * @Route("/")
     */
    public function index(){
        return new Response('My first page!');
    }

    /**
     * @Route("/news")
     */
    public function show(){
        return new Response('Future page to show one space article!');
    }
}