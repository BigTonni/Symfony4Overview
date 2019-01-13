<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

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
     * @param ArticleRepository $articles
     * @param CommentRepository $comments
     * @return Response
     * @Route("/{_locale}", name="article_index", requirements={"_locale" : "en|ru"})
     */
    public function index(ArticleRepository $articles, CommentRepository $comments): Response
    {
        $this->breadcrumbs->addRouteItem('Article', 'article_index');
        $latestArticles = $articles->findLatest();
        //Find 5 first comments
        $oldestComments = $comments->findOldest(5);

        return $this->render('article/index.html.twig', ['articles' => $latestArticles, 'comments' => $oldestComments]);
    }

    /**
     * @Route("/{_locale}/article/list", name="article_list", requirements={"_locale" : "en|ru"})
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function articleList(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Article::class)->createQueryBuilder('a')->getQuery();
        $articles = $paginator->paginate($query, $request->query->getInt('page', 1), 5);

        return $this->render('article/article_list.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @param Article $article
     * @Route("/{_locale}/article/{id}", methods={"GET", "POST"}, name="article_show", requirements={"id" : "\d+", "_locale" : "en|ru"}, defaults={"id" = 1})
     * @return Response
     */
    public function articleShow(Article $article): Response
    {
//        $this->breadcrumbs->addItem('Home', $this->get('router')->generate('index'));
        $this->breadcrumbs->addItem($article->getTitle());

        return $this->render('article/article_show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @param Request $request
     * @Route("/{_locale}/article/new", name="article_new", requirements={"_locale" : "en|ru"})
     * @return Response
     */
    public function articleNew(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $this->addFlash(
                'notice',
                $this->translator->trans('notification.article_created', [
                    '%title%' => $article->getTitle(),
                ])
            );

            return $this->redirectToRoute('article_list');
        }

        return $this->render('article/article_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Article $article
     * @Route("/{_locale}/article/edit/{id}", name="article_edit", requirements={"id" : "\d+", "_locale" : "en|ru"}, defaults={"id" = 1})
     * @return Response
     */
    public function articleEdit(Request $request, Article $article): Response
    {
        $this->denyAccessUnlessGranted('edit', $article);

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash(
                'notice',
                $this->translator->trans('notification.article_edited', [
                    '%title%' => $article->getTitle(),
                ])
            );

            return $this->redirectToRoute('article_list');
        }

        return $this->render('article/article_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Article $article
     * @Route("/{_locale}/article/delete/{id}", name="article_delete", requirements={"id" : "\d+", "_locale" : "en|ru"})
     * @return Response
     */
    public function articleDelete(Article $article): Response
    {
        $this->denyAccessUnlessGranted('delete', $article);

        $em = $this->getDoctrine()->getManager();
        $article->getTags()->clear();
        $article->getComments()->clear();
        $em->remove($article);
        $em->flush();

        $this->addFlash(
            'notice',
            $this->translator->trans('notification.article_deleted', [
                '%title%' => $article->getTitle(),
            ])
        );

        return $this->redirectToRoute('article_list');
    }

    /**
     * @Route("/{_locale}article/{id}/comment/new", name="comment_new", methods={"POST"}, requirements={"_locale" : "en|ru"})
     * @param Request $request
     * @param Article $article
     * @return Response
     */
    public function commentNew(Request $request, Article $article): Response
    {
        $comment = new Comment();
        $comment->setAuthor($article->getAuthor());
        $comment->setPublishedAt(new \DateTime());
        $article->addComment($comment);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
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
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }
}
