<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/{_locale}/admin/article", requirements={"_locale" : "en|ru"}, defaults={"_locale" : "en"})
 * @IsGranted("ROLE_ADMIN")
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
     * @Route("/", methods={"GET"}, name="admin_index")
     * @Route("/", methods={"GET"}, name="admin_article_index")
     * @param ArticleRepository $articles
     * @return Response
     */
    public function index(ArticleRepository $articles): Response
    {
        $authorArticles = $articles->findBy(['author' => $this->getUser()]);

        return $this->render('admin/article/index.html.twig', ['articles' => $authorArticles]);
    }

    /**
     * @Route("/new", methods={"GET", "POST"}, name="admin_article_new")
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
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $this->addFlash(
                'notice',
                $this->translator->trans('article.created_successfully')
            );

            return $this->redirectToRoute('admin_article_index');
        }

        return $this->render('admin/article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Article $article
     * @Route("/{slug}", methods={"GET"}, name="admin_article_show")
     * @return Response
     */
    public function show(Article $article): Response
    {
        // This security check can also be performed
        // using an annotation: @IsGranted("show", subject="article", message="Articles can only be shown to their authors.")
        $this->denyAccessUnlessGranted('show', $article, 'article.can_shown_their_authors.');

        return $this->render('admin/article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/edit/{id<\d+>}", methods={"GET", "POST"}, name="admin_article_edit")
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
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash(
                'notice',
                $this->translator->trans('article.edited_successfully', [
                    '%title%' => $article->getTitle(),
                ])
            );

            return $this->redirectToRoute('admin_article_edit', ['id' => $article->getId()]);
        }

        return $this->render('admin/article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id<\d+>}", methods={"POST"}, name="admin_article_delete")
     * @IsGranted("delete", subject="article")
     * @param Request $request
     * @param Article $article
     * @return Response
     */
    public function delete(Request $request, Article $article): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_article_index');
        }
        $article->getTags()->clear();
        $article->getComments()->clear();

        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        $this->addFlash(
            'notice',
            $this->translator->trans('article.deleted_successfully')
        );

        return $this->redirectToRoute('admin_article_index');
    }
}
