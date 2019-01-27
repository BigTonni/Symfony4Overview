<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    private $translator;

    private $breadcrumbs;

    /**
     * @param TranslatorInterface $translator
     * @param Breadcrumbs $breadcrumbs
     */
    public function __construct(TranslatorInterface $translator, Breadcrumbs $breadcrumbs)
    {
        $this->translator = $translator;
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param int $countItemsPerPage
     * @return Response
     * @Route("/", methods={"GET"}, name="article_index", requirements={"countItemsPerPage" = "\d+"}, defaults={"countItemsPerPage" : "5"})
     */
    public function index(Request $request, PaginatorInterface $paginator, $countItemsPerPage): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Article::class)->createQueryBuilder('a')->getQuery();
        $articles = $paginator->paginate($query, $request->query->getInt('page', 1), $countItemsPerPage);

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/article/list", name="article_list")
     * @return Response
     */
    public function articleList(): Response
    {
        return $this->render('article/list.html.twig');
    }

    /**
     * @param Article $article
     * @Route("/article/{slug}", methods={"GET"}, name="article_show")
     * @return Response
     */
    public function articleShow(Article $article): Response
    {
        $this->breadcrumbs->prependRouteItem('menu.home', 'homepage');
//        $this->breadcrumbs->addRouteItem($article->getTitle(), 'article_show');

        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/comment/{articleSlug}/new", methods={"POST"}, name="comment_new")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @ParamConverter("article", options={"mapping" : {"articleSlug" : "slug"}})
     * @param Request $request
     * @param Article $article
     * @return Response
     */
    public function commentNew(Request $request, Article $article): Response
    {
        $comment = new Comment();
        $comment->setAuthor($this->getUser());
        $comment->setPublishedAt(new \DateTime());
        $article->addComment($comment);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('article_show', ['slug' => $article->getSlug()]);
        }

        return $this->render('article/comment_form_error.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Article $article
     * @return Response
     */
    public function commentForm(Article $article): Response
    {
        $form = $this->createForm(CommentType::class);

        return $this->render('article/_comment_form.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search", methods={"GET"}, name="article_search")
     * @param Request $request
     * @return Response
     */
    public function search(Request $request): Response
    {
        return $this->render('article/search.html.twig');
    }
}
