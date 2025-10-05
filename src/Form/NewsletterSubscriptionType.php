<?php

namespace App\Form;

use App\Entity\NewsletterSubscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsletterSubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse email',
                'attr' => [
                    'placeholder' => 'exemple@email.com',
                    'class' => 'form-control'
                ]
            ])
            ->add('frequency', ChoiceType::class, [
                'label' => 'Fréquence d\'envoi',
                'choices' => [
                    'Hebdomadaire (recommandé)' => 'weekly',
                    'Mensuelle' => 'monthly'
                ],
                'expanded' => true,
                'multiple' => false,
                'data' => 'weekly',
                'attr' => ['class' => 'form-check-input']
            ])
            ->add('subscribe', SubmitType::class, [
                'label' => 'S\'abonner à la newsletter',
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NewsletterSubscription::class,
        ]);
    }
}