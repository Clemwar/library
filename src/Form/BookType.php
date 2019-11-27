<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class ,[
                'label' => 'Titre'
            ])
            ->add('nbPages', IntegerType::class,[
                'label' => 'Nombre de pages'
            ])
            ->add('style', TextType::class ,[
                'label' => 'Genre'
            ])
            ->add('inStock', CheckboxType::class ,[
                'label' => 'En stock'
            ])
            ->add('author', EntityType::class, [
                'class'   => Author::class,
                'choice_label' => 'name',
                'label' => 'Auteur'
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
