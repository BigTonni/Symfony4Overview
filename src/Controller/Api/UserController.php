<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SWG\Tag(name="Users")
 * @Security(name="Bearer")
 */
class UserController extends BaseRestController
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/users")
     * @SWG\Response(
     *     response=200,
     *     description="Returns users",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     *
     * @return JsonResponse|\FOS\RestBundle\View\View
     */
    public function listUsers()
    {
        $users = $this->em->getRepository(User::class)->findAll();
        if (!$users) {
            return new JsonResponse(['message' => 'Users not found'], Response::HTTP_NOT_FOUND);
        }

        $formatted = [];
        foreach ($users as $user) {
            $formatted[] = [
                'id' => $user->getId(),
                'full name' => $user->getFullName(),
                'user name' => $user->getUsername(),
                'email' => $user->getEmail(),
                'created at' => $user->getCreatedAt(),
            ];
        }

        return $this->view($formatted, Response::HTTP_OK, [], [
            'full'
        ]);
    }

    /**
     * @Rest\View()
     * @Rest\Get("/users/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Returns a user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     *
     * @param $id
     * @return JsonResponse|\FOS\RestBundle\View\View
     */
    public function show($id)
    {
        $user = $this->em->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        $formatted = [
            'id' => $user->getId(),
            'full name' => $user->getFullName(),
            'user name' => $user->getUsername(),
            'email' => $user->getEmail(),
            'created at' => $user->getCreatedAt(),
        ];

        return $this->view($formatted, Response::HTTP_OK, [], [
            'full'
        ]);
    }

    /**
     * @Rest\View()
     * @Rest\Post("/users")
     * @SWG\Response(
     *     response=200,
     *     description="Create a new user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     *
     * @param Request $request
     * @return \Symfony\Component\Form\FormInterface|\FOS\RestBundle\View\View
     */
    public function new(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->submit($request->request->all());
        if ($form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();

            return $this->view($user, Response::HTTP_CREATED, [], [
                'full'
            ]);
        }

        return $form;
    }

    /**
     * @Rest\View()
     * @Rest\Put("/users/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Update the user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     *
     * @param Request $request
     * @param User    $user
     * @return JsonResponse|\Symfony\Component\Form\FormInterface|\FOS\RestBundle\View\View
     */
    public function update(Request $request, User $user)
    {
        $user = $this->em->getRepository(User::class)->find($user->getId());
        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(UserType::class, $user, [
            'method' => 'put',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->view($user, Response::HTTP_OK, [], [
                'full'
            ]);
        }

        return $form;
    }

    /**
     * @Rest\View()
     * @Rest\Delete("/users/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Delete the user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     *
     * @param User $user
     * @return JsonResponse
     */
    public function remove(User $user)
    {
        $user = $this->em->getRepository(User::class)->find($user->getId());
        if ($user) {
            $this->em->remove($user);
            $this->em->flush();

            return new JsonResponse(null, 204);
        }

        return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
    }
}
