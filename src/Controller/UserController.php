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
    public $users = [
        ['id' => 1, 'fullName' => 'Test Author1', 'username' => 'Author1', 'email' => 'test@author1.com', 'password' => 'test1'],
        ['id' => 2, 'fullName' => 'Test Author2', 'username' => 'Author2', 'email' => 'test@author2.com', 'password' => 'test2'],
        ['id' => 3, 'fullName' => 'Test Author3', 'username' => 'Author3', 'email' => 'test@author3.com', 'password' => 'test3'],
    ];

    /**
     * @Route("/user/list", name="user_list")
     */
    public function userList(): Response
    {
        return $this->render('user/user_list.html.twig', [
            'users' => $this->users,
        ]);
    }

    /**
     * @Route("/user/{id}", methods={"GET"}, name="user_show", requirements={"id"="\d+"}, defaults={"id"=1})
     */
    public function userShow($id): Response
    {
        $user = [];
        foreach ($this->users as $key => $user_val) {
            if ($user_val['id'] == $id) {
                $user = $this->users[$key];
            }
        }

        return $this->render('user/user_show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/user/new", name="user_new")
     */
    public function userNew(Request $request): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            dump($user);

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/user_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/{id}/edit", name="user_edit", requirements={"id"="\d+"}, defaults={"id"=1})
     */
    public function articleEdit(Request $request, $id): Response
    {
        $user_arr = [];
        foreach ($this->users as $key => $user_val) {
            if ($user_val['id'] == $id) {
                $user_arr[] = $this->users[$key];
            }
        }
        //Create a test object while temporarily the database does not exist
        $user = new User();
        $user->setFullName('authorTest');
        $user->setUsername('authorTest');
        $user->setEmail('author@test.com');
        $user->setPassword('authorTest');

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            dump($user);

            return $this->redirectToRoute('user_list');
        }
        return $this->render('user/user_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
