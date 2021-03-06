<?php

namespace App\Controller\Web;

use App\Entity\Article;
use App\Entity\Like;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_locale}/article", requirements={"_locale" : "en|ru"}, defaults={"_locale" : "en"})
 */
class LikeController extends AbstractController
{
    /**
     * @Route("/like/{slug}", name="article_toggle_like", methods={"POST"})
     * @param Article $article
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @return JsonResponse
     */
    public function toggleArticleLike(Article $article): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $likes = $article->getLikes();

        if ($likes->isEmpty()) {
            $like = new Like();
            $like->setUser($this->getUser());
            $article->addLike($like);

            $em->persist($article);
        } else {
            $isDislike = false;
            foreach ($likes as $like) {
                if ($like->getUser() === $this->getUser()) {
                    $article->removeLike($like);
                    $em->persist($article);
                    $isDislike = true;
                }
            }

            if (!$isDislike) {
                $like = new Like();
                $like->setUser($this->getUser());
                $article->addLike($like);

                $em->persist($article);
            }
        }
        $em->flush();

        $countLikes = $em->getRepository(Like::class)->getCountLikesForArticle($article->getId());

        return new JsonResponse(['like' => $countLikes]);
    }
}
