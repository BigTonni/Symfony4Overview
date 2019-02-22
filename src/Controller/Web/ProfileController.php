<?php

namespace App\Controller\Web;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Like;
use App\Form\Type\ChangePasswordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/{_locale}/profile", requirements={"_locale" : "en|ru"}, defaults={"_locale" : "en"})
 * @IsGranted("ROLE_USER")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/", methods={"GET", "POST"}, name="app_profile")
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @return Response
     */
    public function index(): Response
    {
        $user = $this->getUser();
        $userId = $user->getId();
        $em = $this->getDoctrine()->getManager();
        $user->userArticles = $em->getRepository(Article::class)->getCountUserArticles($userId);
        $user->userComments = $em->getRepository(Comment::class)->getCountUserComments($userId);
        $user->userLikes = $em->getRepository(Like::class)->getCountUserLikes($userId);

        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
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
