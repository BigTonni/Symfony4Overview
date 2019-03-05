<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @SWG\Tag(name="Tokens")
 */
class TokenController extends BaseRestController
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Example test user - test@author1.com.
     * @SWG\Response(
     *     response=200,
     *     description="Returns user token"
     * )
     * @SWG\Parameter(
     *     name="email",
     *     in="query",
     *     type="string",
     *     required=true,
     *     description="User email"
     * )
     * @Route("/tokens/current-user", methods={"GET"})
     * @param Request $request
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
     */
    public function getUserTokenAction(Request $request, JWTTokenManagerInterface $JWTManager)
    {
        $email = $request->query->get('email') ?? false;

        $user = $this->em
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->view($JWTManager->create($user), Response::HTTP_OK, [], [
            'full',
        ]);
    }
}
