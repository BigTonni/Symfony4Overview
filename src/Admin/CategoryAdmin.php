<?php

namespace App\Admin;

//use App\Entity\Category;
use RedCode\TreeBundle\Admin\AbstractTreeAdmin;
//use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CategoryAdmin extends AbstractTreeAdmin
{
    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', TextType::class)
            ->add('slug', TextType::class)
            ->add('parent');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('title')
            ->add('createdAt');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('title')
            ->add('slug')
            ->add('createdAt')
            ->add('lvt')
            ->add('lvl')
            ->add('rgt')
//            ->add('_action', null, [
//                'show' => [],
//                'edit' => [],
//                'delete' => [],
//            ])
        ;
    }
}
