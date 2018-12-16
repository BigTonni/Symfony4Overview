<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user/list", name="user_list")
     * @return Response
     */
    public function userList(): Response
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('user/user_list.html.twig', [
            'users' => $users,
        ]);
    }

    /** @param int $id
     * @Route("/user_articles/{id}/", name="user_articles", requirements={"id"="\d+"}, defaults={"id"=1})
     * @param ArticleRepository $articles
     * @return Response
     */
    public function userArticlesList(ArticleRepository $articles, $id): Response
    {
        $authorArticles = $articles->findBy(['author' => $id], ['publishedAt' => 'DESC']);

        return $this->render('user/user_articles.html.twig', [
            'articles' => $authorArticles
        ]);
    }

    /**
     * @param User $user
     * @Route("/user/{id}", methods={"GET", "POST"}, name="user_show", requirements={"id"="\d+"}, defaults={"id"=1})
     * @return Response
     */
    public function userShow(User $user): Response
    {
        return $this->render('user/user_show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @param Request $request
     * @Route("/user/new", name="user_new")
     * @return Response
     */
    public function userNew(Request $request): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            $user = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/user_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param User $user
     * @Route("/user/edit/{id}/", name="user_edit", requirements={"id"="\d+"}, defaults={"id"=1})
     * @return Response
     */
    public function userEdit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/user_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param User $user
     * @Route("/user/delete/{id}", name="user_delete", requirements={"id"="\d+"})
     * @return Response
     */
    public function userDelete(User $user): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('user_list');
    }
}
