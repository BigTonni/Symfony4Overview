<?php

namespace App\Controller\Web;

use App\Entity\Tag;
use App\Form\TagType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/{_locale}/tag", requirements={"_locale" : "en|ru"}, defaults={"_locale" : "en"})
 * @IsGranted("ROLE_ADMIN")
 */
class TagController extends AbstractController
{
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @Route("/list", name="tag_list")
     * @return Response
     */
    public function tagList(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Tag::class)->createQueryBuilder('a')->getQuery();
        $tags = $paginator->paginate($query, $request->query->getInt('page', 1), 5);

        return $this->render('tag/index.html.twig', [
            'tags' => $tags,
        ]);
    }

    /**
     * @param Request $request
     * @Route("/new", name="tag_new")
     * @return Response
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
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
     * @param Tag $tag
     * @Route("/{slug}", methods={"GET"}, name="tag_show")
     * @return Response
     */
    public function show(Tag $tag): Response
    {
        return $this->render('tag/show.html.twig', [
            'tag' => $tag,
        ]);
    }
}