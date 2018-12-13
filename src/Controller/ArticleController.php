<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\ArticleType;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('article/index.html.twig');
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
     * @param int $id
     * @Route("/article/{id}", methods={"GET"}, name="article_show", requirements={"id"="\d+"}, defaults={"id"=1})
     * @return Response
     */
    public function articleShow($id): Response
    {
        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'No article found for id '.$id
            );
        }
        
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
}
