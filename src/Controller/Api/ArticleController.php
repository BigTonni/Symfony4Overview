<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SWG\Tag(name="Articles")
 * @Security(name="Bearer")
 */
class ArticleController extends BaseRestController
{
    private $em;
    private $paginator;

    public function __construct(EntityManagerInterface $entityManager, PaginatorInterface $paginator)
    {
        $this->em = $entityManager;
        $this->paginator = $paginator;
    }

    /**
     * @Rest\Get("/articles")
     * @SWG\Response(
     *     response=200,
     *     description="Returns articles",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Article::class, groups={"full"}))
     *     )
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="integer",
     *     description="Page number"
     * )
     * @SWG\Parameter(
     *     name="count",
     *     in="query",
     *     type="integer",
     *     description="Count items"
     * )
     * @SWG\Parameter(
     *     name="title",
     *     in="query",
     *     type="string",
     *     description="Article title"
     * )
     * @SWG\Parameter(
     *     name="slug",
     *     in="query",
     *     type="string",
     *     description="Article slug"
     * )
     *
     * @param Request $request
     * @return \FOS\RestBundle\View\View|JsonResponse
     */
    public function listArticles(Request $request)
    {
        $filter_title = $request->query->get('title') ?? false;
        $filter_slug = $request->query->get('slug') ?? false;

        $query = $this->em->getRepository(Article::class)->createQueryBuilder('a');

        if ($filter_title) {
            $query->where('a.title LIKE :title')
                ->setParameter('title', '%' . $filter_title . '%');
        }
        if ($filter_slug) {
            $query->andWhere('a.slug LIKE :slug')
                ->setParameter('slug', '%' . $filter_slug . '%');
        }
        $articles = $query->getQuery();

//        $articles = $this->em->getRepository(Article::class)->findAll();
        if (!$articles) {
            return new JsonResponse(['message' => 'Articles not found'], Response::HTTP_NOT_FOUND);
        }

        $page = $request->query->get('page') ?? 1;
        $count = $request->query->get('count') ?? Article::NUM_ITEMS;

        $articles = $this->paginator->paginate(
            $articles,
            $request->query->getInt('page', $page),
            $request->query->getInt('count', $count)
        );

        $formatted = [];
        foreach ($articles as $article) {
            $str_tags = $this->getStringWithTags($article);

            $formatted[] = [
                'id' => $article->getId(),
                'title' => $article->getTitle(),
                'slug' => $article->getSlug(),
                'body' => $article->getBody(),
                'category' => $article->getCategory()->getTitle(),
                'tags' => $str_tags,
                'author' => $article->getAuthor()->getFullName(),
                'created at' => $article->getCreatedAt(),
            ];
        }

        return $this->view($formatted, Response::HTTP_OK, [], [
            'full',
        ]);
    }

    /**
     * @Rest\View()
     * @Rest\Get("/articles/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Returns a article",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Article::class, groups={"full"}))
     *     )
     * )
     * @SWG\Parameter(
     *     in="path",
     *     name="id",
     *     description="Article id",
     *     required=true,
     *     type="integer",
     * )
     *
     * @param $id
     * @return \FOS\RestBundle\View\View|JsonResponse
     */
    public function show(int $id)
    {
        $article = $this->em->getRepository(Article::class)->find($id);
        if (!$article) {
            return new JsonResponse(['message' => 'Article not found'], Response::HTTP_NOT_FOUND);
        }

        $str_tags = $this->getStringWithTags($article);

        $formatted = [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'body' => $article->getBody(),
            'category' => $article->getCategory()->getTitle(),
            'tags' => $str_tags,
            'author' => $article->getAuthor()->getFullName(),
            'created at' => $article->getCreatedAt(),
        ];

        return $this->view($formatted, Response::HTTP_OK, [], [
            'full',
        ]);
    }

    /**
     * @Rest\Post("/articles")
     * @SWG\Response(
     *     response=200,
     *     description="Create a new article",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Article::class, groups={"full"}))
     *     )
     * )
     *
     * @param Request $request
     * @return \FOS\RestBundle\View\View|\Symfony\Component\Form\FormInterface
     */
    public function new(Request $request)
    {
        $article = new Article();
        $article->setAuthor($this->getUser());
        $form = $this->createForm(ArticleType::class, $article);
        $form->submit($request->request->all());
        if ($form->isValid()) {
            $this->em->persist($article);
            $this->em->flush();

            return $this->view($article, Response::HTTP_CREATED, [], [
                'full',
            ]);
        }

        return $form;
    }

    /**
     * @Rest\View()
     * @Rest\Put("/articles/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Update the article",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Article::class, groups={"full"}))
     *     )
     * )
     *
     * @param Request $request
     * @param int     $id
     * @return \FOS\RestBundle\View\View|JsonResponse|\Symfony\Component\Form\FormInterface
     */
    public function update(Request $request, int $id)
    {
        $article = $this->em->getRepository(Article::class)->find($id);
        if (!$article) {
            return new JsonResponse(['message' => 'article.no_found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(ArticleType::class, $article, [
            'method' => 'put',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->view($article, Response::HTTP_OK, [], [
                'full',
            ]);
        }

        return $form;
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/articles/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Update the article",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Article::class, groups={"full"}))
     *     )
     * )
     *
     * @param Request $request
     * @param int     $id
     * @return \FOS\RestBundle\View\View|JsonResponse
     */
    public function patch(Request $request, int $id)
    {
        $article = $this->em->getRepository(Article::class)->find($id);
        if (!$article) {
            return new JsonResponse(['message' => 'article.no_found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->submit($request->request->all(), false);

        if (false === $form->isValid()) {
            return new JsonResponse(['message' => 'article.no_found'], Response::HTTP_BAD_REQUEST);
        }

        $this->em->flush();

        return $this->view($article, Response::HTTP_OK, [], [
            'full',
        ]);
    }

    /**
     * @Rest\View()
     * @Rest\Delete("/articles/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Delete the article",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Article::class, groups={"full"}))
     *     )
     * )
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function remove(int $id)
    {
        $article = $this->em->getRepository(Article::class)->find($id);
        if ($article) {
            $this->em->remove($article);
            $this->em->flush();

            return new JsonResponse(['message' => 'article.deleted_successfully'], Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(['message' => 'article.no_found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * @param Article $article
     * @return string $str_tags
     */
    private function getStringWithTags(Article $article)
    {
        $str_tags = '';
        if (false !== $arr_tags = $article->getTags()->getValues()) {
            $arr_tag_names = [];
            foreach ($arr_tags as $tag) {
                $arr_tag_names[] = $tag->getName();
            }
            $str_tags = implode(',', $arr_tag_names);
        }

        return $str_tags;
    }
}
