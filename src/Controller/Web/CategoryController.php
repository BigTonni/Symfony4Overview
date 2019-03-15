<?php

namespace App\Controller\Web;

use App\Anton\BlogBundle\Service\Paginator;
use App\Entity\Article;
use App\Entity\Category;
//use App\Service\Paginator;
use App\Form\CategoryType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/{_locale}/category", requirements={"_locale" : "en|ru"}, defaults={"_locale" : "en"})
 */
class CategoryController extends AbstractController
{
    private $translator;

    private $breadcrumbs;

    private $servicePaginator;

    /**
     * @param TranslatorInterface $translator
     * @param Breadcrumbs $breadcrumbs
     * @param Paginator $servicePaginator
     */
    public function __construct(TranslatorInterface $translator, Breadcrumbs $breadcrumbs, Paginator $servicePaginator)
    {
        $this->translator = $translator;
        $this->breadcrumbs = $breadcrumbs;
        $this->servicePaginator = $servicePaginator;
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/list", name="category_list")
     * @return Response
     */
    public function categoryList(): Response
    {
        $this->breadcrumbs->prependRouteItem('menu.home', 'homepage');
        $this->breadcrumbs->addRouteItem('categories', 'category_list');

        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render('category/list.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/new", name="category_new")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash(
                'notice',
                $this->translator->trans('notification.category_created', [
                    '%name%' => $category->getTitle(),
                ])
            );

            return $this->redirectToRoute('category_list');
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/edit/{slug}", name="category_edit")
     * @param Request $request
     * @param Category $category
     * @return Response
     */
    public function edit(Request $request, Category $category): Response
    {
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('category_list');
        }

        return $this->render('category/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/{slug}", methods={"GET"}, name="category_show")
     * @param Request $request
     * @param Category $category
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function show(Request $request, Category $category, PaginatorInterface $paginator): Response
    {
        $this->breadcrumbs->prependRouteItem('menu.home', 'homepage');
        $this->breadcrumbs->addRouteItem($category->getTitle(), 'category_show', [
            'slug' => $category->getSlug(),
        ]);

        $query = $this->getDoctrine()->getManager()->getRepository(Article::class)->findArticlesByCategoryId($category->getId());
        $articles = $paginator->paginate($query, $request->query->getInt('page', 1), $this->servicePaginator->getLimit());

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'articles' => $articles,
        ]);
    }
}
