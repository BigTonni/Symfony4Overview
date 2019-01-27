<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    /**
     * @param ArticleRepository $articles
     * @param CommentRepository $comments
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(ArticleRepository $articles, CommentRepository $comments, Request $request, PaginatorInterface $paginator): Response
    {
        //$latestArticles = $articles->findLatest();
        //Find 5 first comments
        $oldestComments = $comments->findOldest(5);

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Article::class)->createQueryBuilder('a')->getQuery();
        $articles = $paginator->paginate($query, $request->query->getInt('page', 1), 5);

        return $this->render('home/index.html.twig', ['pagination' => $articles, 'comments' => $oldestComments]);
    }
}
