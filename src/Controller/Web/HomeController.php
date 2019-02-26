<?php

namespace App\Controller\Web;

use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    /**
     * @param ArticleRepository $articles
     * @return Response
     */
    public function index(ArticleRepository $articles): Response
    {
        $articles = $articles->findLatestPublished();

        return $this->render('default/homepage.html.twig', ['pagination' => $articles]);
    }

    /**
     * @param CommentRepository $comments
     * @throws \Exception
     * @return Response
     */
    public function sidebar(CommentRepository $comments): Response
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        $comments = $comments->findLatest();

        return $this->render('default/sidebar.html.twig', ['comments' => $comments, 'categories' => $categories]);
    }
}
