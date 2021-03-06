<?php

namespace App\Form;

use App\Entity\Article;
use App\Form\Type\ArticleStatusType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
//use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('slug', TextType::class)
            ->add('body', TextareaType::class, ['attr' => ['rows' => 8]])
            ->add('image', ImageType::class, [
                'required' => false,
            ])
            ->add('status', ArticleStatusType::class)
            ->add('category', EntityType::class, ['class' => 'App:Category', 'choice_label' => 'title'])
            ->add('tags', EntityType::class, ['class' => 'App:Tag', 'choice_label' => 'name', 'multiple' => true])
            ->add('save', SubmitType::class, ['label' => 'Save', 'attr' => ['class' => 'btn btn-primary']]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
