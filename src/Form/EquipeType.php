<?php

namespace App\Form;

use App\Entity\Jeu;
use App\Entity\Equipe;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('jeu', EntityType::class, [
                'class' => Jeu::class,
                //  'multiple' => true,
                //'expanded' => false,
                // 'choice_label' => '',
                'label' => 'Sélectionnez un jeu *: ',
                'attr' => [
                    'placeholder' => 'Sélectionnez un jeu...'
                ]
            ])

            ->add('nomEquipe', TextType::class, [
                'label' => 'Nom de l\'équipe *: ',
                'attr' => [
                    'placeholder' => 'Nom de l\'équipe...'
                ]
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Logo de l\'équipe *: ',
                'required' => true,
                'attr' => [
                    'class' => 'banniereTournoi',
                    'placeholder' => 'jpg, png, jpeg, gif, webp'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '5024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                            'image/gif',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => 'Veuillez choisir un fichier de type png, jpeg, jpg, gif ou webp.',
                        'maxSizeMessage' => 'La taille du fichier ne doit pas dépasser 5Mo.'
                    ])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}
