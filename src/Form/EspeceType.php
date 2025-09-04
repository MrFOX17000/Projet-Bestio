<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\Espece;
use App\Entity\Categorisation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class EspeceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomEspece', TextType::class, [
                'label' => 'Nom de l\'espèce',
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
            ])
            ->add('tailleMoy', NumberType::class, [
                'label' => 'Taille moyenne (m)',
            ])
            ->add('poidsMoy', IntegerType::class, [
                'label' => 'Poids moyen (kg)',
            ])
            ->add('gestation', IntegerType::class, [
                'label' => 'Durée de gestation (jours)',
            ])
            ->add('esperanceVie', IntegerType::class, [
                'label' => 'Espérance de vie (années)',
            ])
            ->add('habitat', TextType::class, [
                'label' => 'Habitat',
            ]) 
            ->add('alimentation', TextType::class, [
                'label' => 'Alimentation',
            ])
            ->add('classe', EntityType::class, [
                'class' => Classe::class,
                'choice_label' => 'nom', 
                'multiple' => false,
            ])
            ->add('image', FileType::class, [ //on utilise un FileType pour pouvoir upload des fichiers
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
            ])
         
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Espece::class,
        ]);
    }
}
