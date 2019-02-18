<?php

namespace App\Controller\Api;

use App\Entity\Tag;
use App\Form\TagType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SWG\Tag(name="Tags")
 * @Security(name="Bearer")
 */
class TagController extends BaseRestController
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/tags")
     * @SWG\Response(
     *     response=200,
     *     description="Returns tags",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Tag::class, groups={"full"}))
     *     )
     * )
     *
     * @return JsonResponse|\FOS\RestBundle\View\View
     */
    public function listTags()
    {
        $tags = $this->em->getRepository(Tag::class)->findAll();
        if (!$tags) {
            return new JsonResponse(['message' => 'Tags not found'], Response::HTTP_NOT_FOUND);
        }
        $formatted = [];
        foreach ($tags as $tag) {
            $formatted[] = [
                'id' => $tag->getId(),
                'name' => $tag->getName(),
                'slug' => $tag->getSlug(),
                'created at' => $tag->getCreatedAt(),
            ];
        }

        return $this->view($formatted, Response::HTTP_OK, [], [
            'full'
        ]);
    }

    /**
     * @Rest\View()
     * @Rest\Get("/tags/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Returns a tag",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Tag::class, groups={"full"}))
     *     )
     * )
     *
     * @param $id
     * @return JsonResponse|\FOS\RestBundle\View\View
     */
    public function show($id)
    {
        $tag = $this->em->getRepository(Tag::class)->find($id);
        if (!$tag) {
            return new JsonResponse(['message' => 'Tag not found'], Response::HTTP_NOT_FOUND);
        }
        $formatted = [
            'id' => $tag->getId(),
            'name' => $tag->getName(),
            'slug' => $tag->getSlug(),
            'created at' => $tag->getCreatedAt(),
        ];

        return $this->view($formatted, Response::HTTP_OK, [], [
            'full'
        ]);
    }

    /**
     * @Rest\View()
     * @Rest\Post("/tags")
     * @SWG\Response(
     *     response=200,
     *     description="Create a new tag",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Tag::class, groups={"full"}))
     *     )
     * )
     *
     * @param Request $request
     *
     * @return \Symfony\Component\Form\FormInterface|\FOS\RestBundle\View\View
     */
    public function new(Request $request)
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->submit($request->request->all());
        if ($form->isValid()) {
            $this->em->persist($tag);
            $this->em->flush();

            return $this->view($tag, Response::HTTP_CREATED, [], [
                'full'
            ]);
        }

        return $form;
    }

    /**
     * @Rest\View()
     * @Rest\Put("/tags/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Update the tag",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Tag::class, groups={"full"}))
     *     )
     * )
     *
     * @param Request $request
     * @param Tag     $tag
     *
     * @return JsonResponse|\FOS\RestBundle\View\View
     */
    public function update(Request $request, Tag $tag)
    {
        $tag = $this->em->getRepository(Tag::class)->find($tag->getId());
        if (!$tag) {
            return new JsonResponse(['message' => 'Tag not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(TagType::class, $tag, [
            'method' => 'put',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->view($tag, Response::HTTP_OK, [], [
                'full'
            ]);
        }

        return $form;
    }

    /**
     * @Rest\View()
     * @Rest\Delete("/tags/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Delete the tag",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Tag::class, groups={"full"}))
     *     )
     * )
     *
     * @param Tag $tag
     * @return JsonResponse
     */
    public function remove(Tag $tag)
    {
        $tag = $this->em->getRepository(Tag::class)->find($tag->getId());
        if ($tag) {
            $this->em->remove($tag);
            $this->em->flush();

            return new JsonResponse(null, 204);
        }

        return new JsonResponse(['message' => 'Tag not found'], Response::HTTP_NOT_FOUND);
    }
}
