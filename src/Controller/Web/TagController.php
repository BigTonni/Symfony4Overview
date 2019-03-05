<?php

namespace App\Controller\Web;

use App\Entity\Article;
use App\Entity\Tag;
use App\Form\TagType;
use App\Service\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/{_locale}/tag", requirements={"_locale" : "en|ru"}, defaults={"_locale" : "en"})
 */
class TagController extends AbstractController
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
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @Route("/list", name="tag_list")
     * @return Response
     */
    public function tagList(Request $request, PaginatorInterface $paginator): Response
    {
        $this->breadcrumbs->prependRouteItem('menu.home', 'homepage');
        $this->breadcrumbs->addRouteItem('tags', 'tag_list');

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Tag::class)->createQueryBuilder('a')->getQuery();
        $tags = $paginator->paginate($query, $request->query->getInt('page', 1), 5);

        return $this->render('tag/index.html.twig', [
            'tags' => $tags,
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @Route("/new", name="tag_new")
     * @return Response
     */
    public function new(Request $request): Response
    {
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $tag = new Tag();

        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();

            $this->addFlash(
                'notice',
                $this->translator->trans('notification.tag_created', [
                    '%name%' => $tag->getName(),
                ])
            );

            return $this->redirectToRoute('tag_list');
        }

        return $this->render('tag/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param Tag $tag
     * @Route("/edit/{slug}", name="tag_edit")
     * @return Response
     */
    public function edit(Request $request, Tag $tag): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash(
                'notice',
                $this->translator->trans('notification.tag_edited', [
                    '%name%' => $tag->getName(),
                ])
            );

            return $this->redirectToRoute('tag_list');
        }

        return $this->render('tag/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/{slug}", methods={"GET"}, name="tag_show")
     * @param Request $request
     * @param Tag $tag
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function show(Request $request, Tag $tag, PaginatorInterface $paginator): Response
    {
        $this->breadcrumbs->prependRouteItem('menu.home', 'homepage');
        $this->breadcrumbs->addRouteItem($tag->getName(), 'tag_show', [
            'slug' => $tag->getSlug(),
        ]);

        $query = $this->getDoctrine()->getManager()->getRepository(Article::class)->findArticlesByTagId($tag->getId());
        $articles = $paginator->paginate($query, $request->query->getInt('page', 1), $this->servicePaginator->getLimit());

        return $this->render('tag/show.html.twig', [
            'tag' => $tag,
            'articles' => $articles,
        ]);
    }
}
