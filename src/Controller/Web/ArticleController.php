<?php

namespace App\Controller\Web;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Like;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Service\Article\Manager\ArticleManager;
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

    private $articleManager;

    /**
     * @param TranslatorInterface $translator
     * @param Breadcrumbs $breadcrumbs
     * @param ArticleManager $articleManager
     */
    public function __construct(TranslatorInterface $translator, Breadcrumbs $breadcrumbs, ArticleManager $articleManager)
    {
        $this->translator = $translator;
        $this->breadcrumbs = $breadcrumbs;
        $this->articleManager = $articleManager;
    }

    /**
     * @Route("/", methods={"GET"}, name="article_index")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $this->breadcrumbs->prependRouteItem('menu.home', 'homepage');
        $this->breadcrumbs->addRouteItem('menu.blog', 'article_index');

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Article::class)->createQueryBuilder('a')->getQuery();
        $articles = $paginator->paginate($query, $request->query->getInt('page', 1), self::ARTICLES_PER_PAGE);

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/list-articles", methods={"GET"}, name="list_articles")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function myListArticles(Request $request, PaginatorInterface $paginator): Response
    {
        $this->breadcrumbs->prependRouteItem('menu.home', 'homepage');
        $this->breadcrumbs->addRouteItem('menu.my_articles', 'list_articles');

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Article::class)->findBy(['author' => $this->getUser()]);
        $articles = $paginator->paginate($query, $request->query->getInt('page', 1), self::ARTICLES_PER_PAGE);

        return $this->render('article/list_articles.html.twig', ['articles' => $articles]);
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
     * @Route("/comment/{articleSlug}/new", methods={"POST"}, name="comment_new")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @ParamConverter("article", options={"mapping" : {"articleSlug" : "slug"}})
     * @param Request $request
     * @param Article $article
     * @throws \Exception
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
     * @Route("/new", methods={"GET", "POST"}, name="article_new")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $article = new Article();
        $article->setAuthor($this->getUser());

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleManager->create($article);

            $this->addFlash(
                'notice',
                $this->translator->trans('article.created_successfully')
            );

            return $this->redirectToRoute('list_articles');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{slug}", methods={"GET", "POST"}, name="article_edit")
     * @IsGranted("edit", subject="article", message="Articles can only be edited by their authors.")
     * @param Request $request
     * @param Article $article
     * @return Response
     */
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleManager->edit($article);

            $this->addFlash(
                'notice',
                $this->translator->trans('article.edited_successfully', [
                    '%title%' => $article->getTitle(),
                ])
            );

            return $this->redirectToRoute('article_edit', ['slug' => $article->getSlug()]);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Article $article
     * @Route("/{slug}", methods={"GET"}, name="article_show")
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @return Response
     */
    public function show(Article $article): Response
    {
        // This security check can also be performed
        // using an annotation: @IsGranted("show", subject="article", message="Articles can only be shown to their authors.")
        $this->denyAccessUnlessGranted('show', $article, 'article.can_shown_their_authors.');

        $this->breadcrumbs->prependRouteItem('menu.home', 'homepage');
        $this->breadcrumbs->addRouteItem($article->getCategory()->getTitle(), 'category_show', [
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
     * @Route("/delete/{slug}", methods={"POST"}, name="article_delete")
     * @IsGranted("delete", subject="article")
     * @param Request $request
     * @param Article $article
     * @return Response
     */
    public function delete(Request $request, Article $article): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->redirectToRoute('list_articles');
        }
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('list_articles');
        }
        $article->getTags()->clear();
        $article->getComments()->clear();

        $this->articleManager->remove($article);

        $this->addFlash(
            'notice',
            $this->translator->trans('article.deleted_successfully')
        );

        return $this->redirectToRoute('list_articles');
    }
}
