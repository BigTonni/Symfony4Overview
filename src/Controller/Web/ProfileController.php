<?php

namespace App\Controller\Web;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Like;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
