<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategoryController extends AbstractController
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
     * @Route("/{_locale}/category/list", name="category_list", requirements={"_locale" : "en|ru"})
     * @return Response
     */
    public function categoryList(): Response
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render('category/list.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @param Category $category
     * @Route("/{_locale}/category/{id}", methods={"GET", "POST"}, name="category_show", requirements={"id" : "\d+", "_locale" : "en|ru"}, defaults={"id" = 1})
     * @return Response
     */
    public function categoryShow(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @param Request $request
     * @param Category $category
     * @Route("/{_locale}/category/edit/{id}", name="category_edit", requirements={"id" : "\d+", "_locale" : "en|ru"}, defaults={"id" = 1})
     * @return Response
     */
    public function categoryEdit(Request $request, Category $category): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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
}
