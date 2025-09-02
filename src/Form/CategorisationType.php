<?php

namespace App\Form;

use App\Entity\Categorisation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CategorisationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomCategorisation', TextType::class, [
                'label' => 'Nom de la catégorisation',
                'attr' => [
                    'placeholder' => 'Entrez le nom de la catégorisation',
                ],
            ])
            ->add('specificite', TextType::class, [
                'label' => 'Spécificité',
                'attr' => [
                    'placeholder' => 'Entrez la spécificité',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorisation::class,
        ]);
    }
}
