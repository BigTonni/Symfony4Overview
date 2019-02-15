<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/{_locale}/user", requirements={"_locale" : "en|ru"}, defaults={"_locale" : "en"})
 */
class UserController extends AbstractController
{
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("/list", name="user_list")
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
     * @Route("/articles/{id}", name="user_articles", requirements={"id" : "\d+"}, defaults={"id" = 1})
     * @param ArticleRepository $articles
     * @return Response
     */
    public function articlesList(ArticleRepository $articles, $id): Response
    {
        $authorArticles = $articles->findBy(['author' => $id], ['publishedAt' => 'DESC']);

        return $this->render('user/user_articles.html.twig', [
            'articles' => $authorArticles,
        ]);
    }

    /**
     * @param User $user
     * @Route("/{id}", methods={"GET", "POST"}, name="user_show", requirements={"id" : "\d+"}, defaults={"id" = 1})
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
     * @Route("/new", name="user_new")
     * @return Response
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
     * @Route("/edit/{id}", name="user_edit", requirements={"id" : "\d+"}, defaults={"id" = 1})
     * @return Response
     */
    public function edit(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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
     * @Route("/delete/{id}", name="user_delete", requirements={"id" : "\d+"})
     * @return Response
     */
    public function delete(User $user): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user->getComments()->clear();

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('user_list');
    }
}