<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends AbstractController
{
//    public function register(Request $request, UserPasswordEncoderInterface $encoder)
//    {
//        if ($request->isMethod('POST')) {
//            $em = $this->getDoctrine()->getManager();
//
//            $user = new User();
//            $email = $request->request->get('_email');
//            $user->setEmail($email);
//            $user->setFullName('Test User');
//            $user->setUsername($email);
//            $user->setRoles(['ROLE_USER']);
//
//            $password = $request->request->get('_password');
//
//            $user->setPassword($encoder->encodePassword($user, $password));
//            $em->persist($user);
//            $em->flush();
//
//            return new Response(sprintf('User %s successfully created', $user->getUsername()) . "\n");
//        }
//
//        return new Response('Bad request' . "\n");
//    }

//    public function api()
//    {
//        return new Response(sprintf('Logged in as %s', $this->getUser()->getUsername()) . "\n");
//    }
}
