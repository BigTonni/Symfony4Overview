<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public $users = [
        ['id' => 1, 'fullName' => 'Test Author1', 'username' => 'Author1', 'email' => 'test@author1', 'password' => 'test1'],
        ['id' => 2, 'fullName' => 'Test Author2', 'username' => 'Author2', 'email' => 'test@author2', 'password' => 'test2'],
        ['id' => 3, 'fullName' => 'Test Author3', 'username' => 'Author3', 'email' => 'test@author3', 'password' => 'test3'],
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
}
