<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
    ->add('pseudo', TextType::class, [
        'label' => 'Nom d\'utilisateur :',
        'attr' => [
            'placeholder' => 'Votre nom d\'utilisateur',
        ],
    ])
     ->add('photo', FileType::class, [ //on utilise un FileType pour pouvoir upload des fichiers
                'label' => 'Votre image (JPG/JPEG/PNG)', // On précise à l'utilisateur quels types de fichiers sont acceptés
                'mapped' => false, // Ce champs n'est relié à aucune entité donc mapped = false
                'required' => true,
                'constraints' => [ // On applique des contraintes pour se protéger des failles d'upload
                    new File([
                        'maxSize' => '1024k', //On limite la taille du fichier
                        'mimeTypes' => [ // On limite l'extension de fichier acceptée
                            'image/jpeg', 
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG/JPEG/PNG)', // message d'erreur si le mimeType n'est pas bon
                    ]),
                ],
            ]);
    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}