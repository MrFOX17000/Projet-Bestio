<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
    ->add('email', EmailType::class, [
        'label' => 'Adresse e-mail :',
        'attr' => [
            'placeholder' => 'Votre adresse e-mail',
            'autofocus' => true,
        ],
    ])
    ->add('pseudo', null, [
        'label' => 'Nom d\'utilisateur :',
        'attr' => [
            'placeholder' => 'Votre nom d\'utilisateur',
        ],
    ])
    ->add('photo', null, [
        'label' => 'Photo de profil (URL) :',
        'attr' => [
            'placeholder' => 'Lien vers votre image de profil',
        ],
        'required' => false,
    ]);
    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}