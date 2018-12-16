<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
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
     * @Route("/", name="article_index")
     * @return Response
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
//        $article = $this->getDoctrine()
//            ->getRepository(Article::class)
//            ->find($id);
//
//        if (!$article) {
//            throw $this->createNotFoundException(
//                'No article found for id '.$id
//            );
//        }
        
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
            $article = $form->getData();
            //dump($article);

            $em = $this->getDoctrine()->getManager();
            $category = $this->getCategory();

            $category->setTitle($category->getTitle());
            $em->persist($category);

            $article->setCategory($category);

            $em->persist($article);
            $em->flush();

//            return new Response('Saved new article with id '.$article->getId());
            return $this->redirectToRoute('article_list');
        }
        
        return $this->render('article/article_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @Route("/article/edit/{id}", name="article_edit", requirements={"id"="\d+"}, defaults={"id"=1})
     * @return Response
     */
    public function articleEdit(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'No article found for id '.$id
            );
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            //dump($article);

            $em->flush();

//            return new Response('Updated article with id '.$id);
            return $this->redirectToRoute('article_list');
        }
        return $this->render('article/article_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param int $id
     * @Route("/article/delete/{id}", name="article_delete", requirements={"id"="\d+"})
     * @return Response
     */
    public function articleDelete($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'No article found for id '.$id
            );
        }

        $em->remove($article);
        $em->flush();

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
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $article->addComment($comment);
            $user->addComment($comment);
            $em->persist($comment);
            $em->flush();
        }
        return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
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
