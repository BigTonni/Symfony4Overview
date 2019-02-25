<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Form\Type\ChangePasswordType;
use App\Form\UserType;
use App\Repository\ArticleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("ROLE_USER")
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

        return $this->render('user/list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @param int $id
     * @Route("/articles/{id}", name="user_articles", requirements={"id" : "\d+"}, defaults={"id" = 1})
     * @param ArticleRepository $articles
     * @return Response
     */
    public function articlesList(ArticleRepository $articles, $id): Response
    {
        $authorArticles = $articles->findBy(['author' => $id]);

        return $this->render('user/articles.html.twig', [
            'articles' => $authorArticles,
        ]);
    }

    /**
     * @param User $user
     * @Route("/{id}", methods={"GET", "POST"}, name="user_show", requirements={"id" : "\d+"}, defaults={"id" = 1})
     * @return Response
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @param Request $request
     * @Route("/new", name="user_new")
     * @throws \Exception
     * @return Response
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setCreatedAt(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @Route("/edit", name="user_edit")
     * @return Response
     */
    public function edit(Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'user.updated_successfully');

            return $this->redirectToRoute('user_edit');
        }

        return $this->render('user/edit.html.twig', [
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
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->redirectToRoute('user_list');
        }

        $user->getComments()->clear();

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        $this->addFlash(
            'notice',
            $this->translator->trans('user.deleted_successfully')
        );

        return $this->redirectToRoute('user_list');
    }

    /**
     * @Route("/change-password", methods={"GET", "POST"}, name="profile_change_password")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->encodePassword($user, $form->get('newPassword')->getData()));

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'notice',
                $this->translator->trans('user.change_password_successfully')
            );

            return $this->redirectToRoute('app_logout');
        }

        return $this->render('profile/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
