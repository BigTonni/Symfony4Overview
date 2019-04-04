<?php

namespace App\Controller\Web;

use Anton\BlogBundle\Service\PageLimiter;
use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    /**
     * @param Request $request
     * @param ArticleRepository $articles
     * @param PaginatorInterface $paginator
     * @param PageLimiter $pageLimiter
     * @return Response
     */
    public function index(Request $request, ArticleRepository $articles, PaginatorInterface $paginator, PageLimiter $pageLimiter): Response
    {
        if (false !== $sorting_params = $request->query->get('articles_sorting', '')) {
            $params = explode('-', $sorting_params);
            $query = $articles->findLatestPublishedWithOrder('createdAt', $params[1]);
        } else {
            $query = $articles->findLatestPublished();
        }

        $articles = $paginator->paginate($query, $request->query->getInt('page', 1), $pageLimiter->getLimit());

        return $this->render('default/homepage.html.twig', ['articles' => $articles]);
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
