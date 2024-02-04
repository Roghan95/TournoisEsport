<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('photoFile', VichImageType::class, [
                'label' => 'Photo de profil',
                'required' => true,
                'attr' => [
                    'class' => 'form-control-file'
                ],
                'download_uri' => false, // Ne pas afficher l'image existante
                'allow_delete' => false, // Ne pas afficher le bouton de suppression
                'delete_label' => 'Supprimer la photo', // Changer le label du bouton de suppression
                'download_label' => false, // Changer le label du bouton de téléchargement
                'image_uri' => false, // Ne pas afficher l'image existante
                // 'constraints' => [
                //     new File([
                //         'maxSize' => '5024k',
                //         'mimeTypes' => [
                //             'image/png',
                //             'image/jpeg',
                //             'image/jpg',
                //             'image/gif',
                //             'image/webp'
                //         ],
                //         'mimeTypesMessage' => 'Veuillez choisir un fichier de type png, jpeg, jpg, gif ou webp.',
                //         'maxSizeMessage' => 'La taille du fichier ne doit pas dépasser 5Mo.'
                //     ])
                // ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Modifier',
                'attr' => [
                    'class' => 'btn-block'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
