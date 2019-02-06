<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Like;
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
 * @IsGranted("ROLE_USER")
 * @Route("/{_locale}/article", requirements={"_locale" : "en|ru"}, defaults={"_locale" : "en"})
 */
class ArticleController extends AbstractController
{
    public const ARTICLES_PER_PAGE = 3;

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
     * @Route("/", methods={"GET"}, name="article_index")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Article::class)->createQueryBuilder('a')->getQuery();
        $articles = $paginator->paginate($query, $request->query->getInt('page', 1), self::ARTICLES_PER_PAGE);

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/search", methods={"GET"}, name="article_search")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function search(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Article::class)->findBySearchQuery(
            $request->query->get('search_field', '')
        );

        $articles = $paginator->paginate($query, $request->query->getInt('page', 1), self::ARTICLES_PER_PAGE);

        return $this->render('article/search.html.twig', [
            'articles' => $articles,
            'title' => $this->translator->trans('search.search_title') . ' ' . $request->query->get('search_field'),
        ]);
    }

    /**
     * @Route("/{slug}", methods={"GET"}, name="article_show")
     * @param Article $article
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @return Response
     */
    public function show(Article $article): Response
    {
        $this->breadcrumbs->prependRouteItem('menu.home', 'homepage');
        $this->breadcrumbs->addRouteItem($article->getCategory()->getTitle(), 'category_articles', [
            'slug' => $article->getCategory()->getSlug(),
        ]);
        $this->breadcrumbs->addRouteItem($article->getTitle(), 'article_show', [
            'slug' => $article->getSlug(),
        ]);

        $em = $this->getDoctrine()->getManager();
        $countLikes = $em->getRepository(Like::class)->getCountLikesForArticle($article->getId());

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'like' => $countLikes,
        ]);
    }

    /**
     * @Route("/list_in_category/{slug}", name="category_articles")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param Category $category
     * @return Response
     */
    public function showCategoryArticles(Request $request, PaginatorInterface $paginator, Category $category): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Article::class)->findArticlesByCategoryId($category->getId());
        $articles = $paginator->paginate($query, $request->query->getInt('page', 1), self::ARTICLES_PER_PAGE);

        return $this->render('article/category_articles.html.twig', [
            'articles' => $articles,
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
}
