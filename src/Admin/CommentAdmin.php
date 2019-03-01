<?php

namespace App\Admin;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentAdmin extends AbstractAdmin
{
    /**
     * @param $object
     *
     * @return string|null
     */
    public function toString($object): string
    {
        return $object instanceof Comment
            ? $object->getArticle()
            : 'Comment';
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Content', ['class' => 'col-md-9'])
            ->add('article', EntityType::class, [
                'class' => Article::class,
                'choice_label' => 'slug',
            ])
            ->add('content', TextareaType::class, [
                'attr' => ['class' => 'mceNoEditor'],
            ])
            ->end()
            ->with('Meta data', ['class' => 'col-md-3'])
            ->add('author', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
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
            ->add('article')
            ->add('author');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('content')
            ->add('author')
            ->add('article')
            ->add('publishedAt');
    }
}
