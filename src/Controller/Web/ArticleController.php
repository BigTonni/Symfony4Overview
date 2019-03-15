<?php

namespace App\Controller\Web;

use App\Anton\BlogBundle\Service\Paginator;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Like;
use App\Event\ArticlePublishedEvent;
use App\Event\ArticleViewedEvent;
use App\Form\ArticleType;
//use App\Service\Paginator;
use App\Form\CommentType;
use App\Service\Article\Manager\ArticleManager;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/{_locale}/article", requirements={"_locale" : "en|ru"}, defaults={"_locale" : "en"})
 */
class ArticleController extends AbstractController
{
    private $translator;

    private $breadcrumbs;

    private $articleManager;

    private $servicePaginator;

    /**
     * @param TranslatorInterface $translator
     * @param Breadcrumbs $breadcrumbs
     * @param ArticleManager $articleManager
     * @param Paginator $servicePaginator
     */
    public function __construct(TranslatorInterface $translator, Breadcrumbs $breadcrumbs, ArticleManager $articleManager, Paginator $servicePaginator)
    {
        $this->translator = $translator;
        $this->breadcrumbs = $breadcrumbs;
        $this->articleManager = $articleManager;
        $this->servicePaginator = $servicePaginator;
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
        $query = $em->getRepository(Article::class)->findLatestPublished();
        $articles = $paginator->paginate($query, $request->query->getInt('page', 1), $this->servicePaginator->getLimit());

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/user-articles", methods={"GET"}, name="article_list")
     * @return Response
     */
    public function myListArticles(): Response
    {
        $this->breadcrumbs->prependRouteItem('menu.home', 'homepage');
        $this->breadcrumbs->addRouteItem('menu.my_articles', 'article_list');

        $userId = $this->getUser()->getId();
        $articleRepository = $this->getDoctrine()->getManager()->getRepository(Article::class);
        $publishedArticles = $articleRepository->findPublishedArticlesByUserId($userId);
        $notPublishedArticles = $articleRepository->findNotPublishedArticlesByUserId($userId);

        return $this->render('article/list_articles.html.twig', ['publishedArticles' => $publishedArticles, 'notPublishedArticles' => $notPublishedArticles]);
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

        $articles = $paginator->paginate($query, $request->query->getInt('page', 1), $this->servicePaginator->getLimit());

        return $this->render('article/search.html.twig', [
            'articles' => $articles,
            'title' => $this->translator->trans('search.search_title') . ' ' . $request->query->get('search_field'),
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/comment/{articleSlug}/new", methods={"POST"}, name="comment_new")
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
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/new", methods={"GET", "POST"}, name="article_new")
     * @param Request $request
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     */
    public function new(Request $request, EventDispatcherInterface $eventDispatcher): Response
    {
        //Do any Categories exist?
        if (empty($this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll())) {
            $this->addFlash(
                'notice',
                $this->translator->trans('category.no_exist')
            );

            return $this->redirectToRoute('article_list');
        }

        $article = new Article();
        $article->setAuthor($this->getUser());

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleManager->create($article);

            $eventDispatcher->dispatch(ArticlePublishedEvent::NAME, new ArticlePublishedEvent($article));

            $this->addFlash(
                'notice',
                $this->translator->trans('article.created_successfully')
            );

            return $this->redirectToRoute('article_list');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/edit/{slug}", methods={"GET", "POST"}, name="article_edit")
//     * @IsGranted("edit", subject="article", message="Articles can only be edited by their authors.")
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

            return $this->redirectToRoute('article_show', ['slug' => $article->getSlug()]);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/subscriptions", methods={"GET"}, name="articles_in_subscribed_categories")
     * @param Request            $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function showNewArticlesInSubscribedCategories(Request $request, PaginatorInterface $paginator): Response
    {
        $query = $this->articleManager->getNotReadArticles();

        $query_articles = [];
        foreach ($query as $article) {
            $query_articles[] = $article->getArticle();
        }

        $articles = $paginator->paginate($query_articles, $request->query->getInt('page', 1), $this->servicePaginator->getLimit());

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @param Article $article
     * @param EventDispatcherInterface $eventDispatcher
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @return Response
     * @Route("/{slug}", methods={"GET"}, name="article_show")
     */
    public function show(Article $article, EventDispatcherInterface $eventDispatcher): Response
    {
        // This security check can also be performed
        // using an annotation: @IsGranted("show", subject="article", message="Articles can only be shown to their authors.")
//        $this->denyAccessUnlessGranted('show', $article, 'article.can_shown_their_authors.');

        $this->breadcrumbs->prependRouteItem('menu.home', 'homepage');
        $this->breadcrumbs->addRouteItem($article->getCategory()->getTitle(), 'category_show', [
            'slug' => $article->getCategory()->getSlug(),
        ]);
        $this->breadcrumbs->addRouteItem($article->getTitle(), 'article_show', [
            'slug' => $article->getSlug(),
        ]);

        $em = $this->getDoctrine()->getManager();
        $countLikes = $em->getRepository(Like::class)->getCountLikesForArticle($article->getId());

        $eventDispatcher->dispatch(ArticleViewedEvent::NAME, new ArticleViewedEvent($article));

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
            $this->addFlash(
                'notice',
                $this->translator->trans('article.can_delete_only_admin')
            );

            return $this->redirectToRoute('article_list');
        }

        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('article_list');
        }

        $article->getTags()->clear();
        $article->getComments()->clear();

        $this->articleManager->remove($article);

        $this->addFlash(
            'notice',
            $this->translator->trans('article.deleted_successfully')
        );

        return $this->redirectToRoute('article_list');
    }
}
