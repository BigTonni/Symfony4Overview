<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Subscription;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/category")
 */
class SubscriptionController extends AbstractController
{
    /**
     * @Route("/subscribe/{slug}", name="category_subscribe")
     * @param Category $category
     * @return Response
     */
    public function subscribe(Category $category): Response
    {
        $em = $this->getDoctrine()->getManager();
        $isSubscription = $em->getRepository(Subscription::class)->findBy(
            [
                'user' => $this->getUser(),
                'category' => $category,
            ]
        );
        if (!$isSubscription) {
            $subscribers = new Subscription();
            $subscribers->setUser($this->getUser());
            $subscribers->setCategory($category);
            $subscribers->setIsSend(false);
            $em->persist($subscribers);
        }
        $em->flush();

        $this->addFlash(
            'notice', 'Subscription created successfully'
        );

        return $this->redirectToRoute('category_list');
    }

    /**
     * @Route("/unsubscribe/{slug}", name="category_unsubscribe")
     * @param Category $category
     * @return Response
     */
    public function unsubscribe(Category $category): Response
    {
        $em = $this->getDoctrine()->getManager();
        $arrSubscription = $em->getRepository(Subscription::class)->findBy(
            [
                'user' => $this->getUser(),
                'category' => $category,
            ]
        );

        if ($arrSubscription) {
            foreach ($arrSubscription as $subscription) {
                $em->remove($subscription);
            }
            $em->flush();

            $this->addFlash(
                'notice', 'Subscription deleted successfully'
            );
        }

        return $this->redirectToRoute('category_list');
    }
}
