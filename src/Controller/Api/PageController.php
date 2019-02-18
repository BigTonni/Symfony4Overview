<?php

namespace App\Controller\Api;

use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SWG\Tag(name="Pages")
 * @Security(name="Bearer")
 */
class PageController extends BaseRestController
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/pages")
     * @SWG\Response(
     *     response=200,
     *     description="Returns pages",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Page::class, groups={"full"}))
     *     )
     * )
     *
     * @return \FOS\RestBundle\View\View
     */
    public function listPages()
    {
        $pages = $this->em->getRepository(Page::class)->findAll();

        $formatted = [];
        foreach ($pages as $page) {

            $formatted[] = [
                'id' => $page->getId(),
                'title' => $page->getTitle(),
                'slug' => $page->getSlug(),
                'body' => $page->getBody(),
                'author' => $page->getAuthor()->getFullName(),
                'created at' => $page->getCreatedAt(),
            ];
        }

        return $this->view($formatted, Response::HTTP_OK, [], [
            'full'
        ]);
    }

    /**
     * @Rest\View()
     * @Rest\Get("/pages/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Returns a page",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Page::class, groups={"full"}))
     *     )
     * )
     *
     * @param $id
     *
     * @return JsonResponse|\FOS\RestBundle\View\View
     */
    public function show(int $id)
    {
        $page = $this->em->getRepository(Page::class)->find($id);
        if (!$page) {
            return new JsonResponse(['message' => 'Page not found'], Response::HTTP_NOT_FOUND);
        }

        $formatted = [
            'id' => $page->getId(),
            'title' => $page->getTitle(),
            'slug' => $page->getSlug(),
            'body' => $page->getBody(),
            'author' => $page->getAuthor()->getFullName(),
            'created at' => $page->getCreatedAt(),
        ];

        return $this->view($formatted, Response::HTTP_OK, [], [
            'full'
        ]);
    }
}
