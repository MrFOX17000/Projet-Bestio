<?php

namespace App\Form;

use App\Entity\Categorisation;
use App\Entity\Classe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ClasseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la classe :',
                'attr' => [
                    'placeholder' => 'Entrez le nom de la classe',
                ],
            ])
            ->add('specificite', TextType::class, [
                'label' => 'Spécificité :',
                'attr' => [
                    'placeholder' => 'Entrez la spécificité',
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image principale',
                'mapped' => false,
                'required' => false,
                'attr' => ['accept' => 'image/*']
            ])
            ->add('newImages', FileType::class, [
                'label' => 'Images supplémentaires (max 2)',
                'mapped' => false,
                'multiple' => true,
                'required' => false,
                'attr' => ['accept' => 'image/*']
            ])
            ->add('appartenir', EntityType::class, [
                'class' => Categorisation::class,
                'choice_label' => 'nomCategorisation',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Classe::class,
        ]);
    }
}
