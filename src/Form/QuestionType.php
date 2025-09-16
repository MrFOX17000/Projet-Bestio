<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Espece;
use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titreQuestion', TextType::class, [
                'label' => 'Titre de la question :',
                'attr' => ['placeholder' => 'Votre titre...']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Votre question :',
                'attr' => ['placeholder' => 'Décrivez votre question...']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
