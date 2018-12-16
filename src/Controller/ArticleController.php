<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @param ArticleRepository $articles
     * @param CommentRepository $comments
     * @return Response
     * @Route("/", name="article_index")
     */
    public function index(ArticleRepository $articles, CommentRepository $comments): Response
    {
        $latestArticles = $articles->findLatest();
        //Find 5 first comments
        $oldestComments = $comments->findOldest(5);
        return $this->render('article/index.html.twig', ['articles' => $latestArticles, 'comments' => $oldestComments]);
    }
    
    /**
     * @Route("/article/list", name="article_list")
     * @return Response
     */
    public function articleList(): Response
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();

        return $this->render('article/article_list.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @param Article $article
     * @Route("/article/{id}", methods={"GET", "POST"}, name="article_show", requirements={"id"="\d+"}, defaults={"id"=1})
     * @return Response
     */
    public function articleShow(Article $article): Response
    {
        return $this->render('article/article_show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @param Request $request
     * @Route("/article/new", name="article_new")
     * @return Response
     */
    public function articleNew(Request $request): Response
    {
        $article = new Article();
        
        $form = $this->createForm(ArticleType::class, $article);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
//            $article = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'Article create');
            return $this->redirectToRoute('article_list');
        }
        
        return $this->render('article/article_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Article $article
     * @Route("/article/edit/{id}", name="article_edit", requirements={"id"="\d+"}, defaults={"id"=1})
     * @return Response
     */
    public function articleEdit(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
//            $article = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'Article edit');
            return $this->redirectToRoute('article_list');
        }
        return $this->render('article/article_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Article $article
     * @Route("/article/delete/{id}", name="article_delete", requirements={"id"="\d+"})
     * @return Response
     */
    public function articleDelete(Article $article): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        $this->addFlash('success', 'Article delete');

        return $this->redirectToRoute('article_list');
    }

    /**
     * @Route("article/{id}/comment/new", name="comment_new", methods={"POST"})
     * @param Request $request
     * @param Article $article
     * @return Response
     */
    public function newComment(Request $request, Article $article): Response
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
