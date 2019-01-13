<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @Route("/{_locale}/tag/list", name="tag_list", requirements={"_locale" : "en|ru"})
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Tag::class)->createQueryBuilder('a')->getQuery();
        $tags = $paginator->paginate($query, $request->query->getInt('page', 1), 5);

        return $this->render('tag/index.html.twig', [
            'tags' => $tags,
        ]);
    }

    /**
     * @param Tag $tag
     * @Route("/{_locale}/tag/{id}", methods={"GET", "POST"}, name="tag_show", requirements={"id" : "\d+", "_locale" : "en|ru"}, defaults={"id" = 1})
     * @return Response
     */
    public function tagShow(Tag $tag): Response
    {
        return $this->render('tag/show.html.twig', [
            'tag' => $tag,
        ]);
    }

    /**
     * @param Request $request
     * @Route("/{_locale}/tag/new", name="tag_new", requirements={"_locale" : "en|ru"})
     * @return Response
     */
    public function tagNew(Request $request): Response
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
     * @Route("/{_locale}/tag/edit/{id}", name="tag_edit", requirements={"id" : "\d+", "_locale" : "en|ru"}, defaults={"id" = 1})
     * @return Response
     */
    public function tagEdit(Request $request, Tag $tag): Response
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
}
