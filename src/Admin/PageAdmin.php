<?php

namespace App\Admin;

use App\Entity\Page;
use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PageAdmin extends AbstractAdmin
{
    /**
     * @param $object
     *
     * @return string|null
     */
    public function toString($object): ?string
    {
        return $object instanceof Page
            ? $object->getTitle()
            : 'Page';
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Content', ['class' => 'col-md-9'])
            ->add('title', TextType::class)
            ->add('body', TextareaType::class, [
                'attr' => ['class' => 'ckeditor'],
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
        $datagridMapper->add('title');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('isPublished')
            ->add('slug')
            ->add('author')
            ->add('createdAt')
        ;
    }
}
