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
    
    public $articles = [
            ['title' => 'First', 'slug' => '1', 'author' => 'Author1', 'body' => 'Desc1', 'publishedAt' => '04-12-2018'],
            ['title' => 'Second', 'slug' => '2', 'author' => 'Author2', 'body' => 'Desc2', 'publishedAt' => '04-12-2018'],
            ['title' => 'Third', 'slug' => '3', 'author' => 'Author3', 'body' => 'Desc3', 'publishedAt' => '04-12-2018'],
        ];
    /**
     * @Route("/", name="blog_index")
     */
    public function index(): Response
    {
        return $this->render('article/index.html.twig');
    }
    
    /**
     * @Route("/blog/list", name="blog_list")
     */
    public function blogList(): Response
    {
        return $this->render('article/article_list.html.twig', [
            'articles' => $this->articles,
        ]);
    }

    /**
     * @Route("/article/{slug}", methods={"GET"}, name="blog_article", requirements={"slug"="\d+"})
     */
    public function articleShow($slug = 1): Response
    {
        $article = [];
        foreach ($this->articles as $key => $article_val) {
            if ($article_val['slug'] == $slug) {
                $article = $this->articles[$key];
            }
        }
        
        return $this->render('article/article_show.html.twig', [
            'article' => $article,
        ]);
    }
    
    /**
     * @Route("/article/new", name="article_new")
     */
    public function articleNew(Request $request): Response
    {
        $article = new Article();
        
        $form = $this->createForm(ArticleType::class, $article);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            dump($article);
            
            return $this->redirectToRoute('blog_list');
        }
        
        return $this->render('article/article_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/article/{slug}/edit", name="article_edit", requirements={"slug"="\d+"})
     */
    public function articleEdit(Request $request, $slug = 1): Response
    {        
        $article_arr = [];
        foreach ($this->articles as $key => $article_val) {
            if ($article_val['slug'] == $slug) {
                $article_arr = $this->articles[$key];
            }
        }
        //Create a test object while temporarily the database does not exist
        $article = new Article();
        $article->setTitle($article_arr['title']);
        $article->setSlug($article_arr['slug']);
        $article->setBody($article_arr['body']);
//        $article->setPublishedAt($article_arr['publishedAt']);
        
        $user = new User();
        $user->setFullName('authorTest');
        $user->setUsername('authorTest');
        $user->setEmail('author@test.com');
        $user->setPassword('authorTest');
        
        $article->setAuthor($user);

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            dump($article);
            
            return $this->redirectToRoute('blog_list');
        }
        return $this->render('article/article_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
