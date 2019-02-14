<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;

use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ArticleController extends AbstractFOSRestController implements ClassResourceInterface
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/articles")
     * @SWG\Response(
     *     response=200,
     *     description="Returns articles",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Article::class, groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Articles")
     *
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
     * @QueryParam(name="limit", requirements="\d+", strict=true, nullable=true, description="limit")
     *
     * @param ParamFetcher $paramFetcher
     */
    public function listArticles(ParamFetcher $paramFetcher)
    {
        $articles = $this->em->getRepository(Article::class)->findAll();

        $dynamicQueryParam = new QueryParam();
        $dynamicQueryParam->name = "limit";
        $dynamicQueryParam->requirements = "\d+";
        $paramFetcher->addParam($dynamicQueryParam);

        $page = $paramFetcher->get('page');

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

        return array('articles' => $formatted, 'page' => $page);
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
     * @SWG\Tag(name="Articles")
     *
     * @param $id
     *
     * @return JsonResponse|View
     */
    public function show(int $id)
    {
        $article = $this->em->getRepository(Article::class)->find($id);
        if (empty($article)) {
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
        $view = View::create($formatted, Response::HTTP_OK);
        $view->setFormat('json');

        return $view;
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
     * @SWG\Tag(name="Articles")
     *
     * @param Request $request
     *
     * @return Article|\Symfony\Component\Form\FormInterface
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

            return View::create($article, Response::HTTP_CREATED);
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
     * @SWG\Tag(name="Articles")
     *
     * @param Request $request
     * @param int     $id
     *
     * @return JsonResponse|\Symfony\Component\Form\FormInterface
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

            return View::create($article, Codes::HTTP_NO_CONTENT);
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
     * @SWG\Tag(name="Articles")
     *
     * @param Request $request
     * @param int     $id
     *
     * @return JsonResponse|\Symfony\Component\Form\FormInterface
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
            return $this->view($form);
        }

        $this->em->flush();

        return View::create(null, Response::HTTP_NO_CONTENT);
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
     * @SWG\Tag(name="Articles")
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
