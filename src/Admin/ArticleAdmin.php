<?php

namespace App\Admin;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
//use App\Entity\Like;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ArticleAdmin extends AbstractAdmin
{
    /**
     * @param $object
     *
     * @return string|null
     */
    public function toString($object): ?string
    {
        return $object instanceof Article
            ? $object->getTitle()
            : 'Article';
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Content', ['class' => 'col-md-9'])
            ->add('title', TextType::class)
            ->add('slug', TextType::class)
            ->add('body', TextareaType::class, [
//                'attr' => ['class' => 'ckeditor'],
            ])
            ->end()
            ->with('Meta data', ['class' => 'col-md-3'])
            ->add('category', ModelType::class, [
                'class' => Category::class,
                'property' => 'title',
            ])
            ->add('tags', EntityType::class, ['class' => 'App:Tag', 'choice_label' => 'name', 'multiple' => true])
            ->add('author', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'userName',
            ])
            ->end()
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('category', null, [], EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
            ])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('status')
            ->add('slug')
            ->add('author.fullName')
            ->add('category.title')
//            ->add('likes')
            ->add('createdAt')
        ;
    }
}
