<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
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

    /**
     * @param int $id
     * @Route("/user/{id}", methods={"GET"}, name="user_show", requirements={"id"="\d+"}, defaults={"id"=1})
     * @return Response
     */
    public function userShow($id): Response
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        }

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
            $user = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

//            return new Response('Saved new user with id '.$user->getId());
            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/user_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @Route("/user/edit/{id}/", name="user_edit", requirements={"id"="\d+"}, defaults={"id"=1})
     * @return Response
     */
    public function userEdit(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        }

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $em->flush();

//            return new Response('Updated user with id '.$id);
            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/user_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param int $id
     * @Route("/user/delete/{id}", name="user_delete", requirements={"id"="\d+"})
     * @return Response
     */
    public function userDelete($id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        }

        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('user_list');
    }
}
