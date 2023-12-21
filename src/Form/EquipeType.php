<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\Jeu;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('jeu', EntityType::class, [
                'class' => Jeu::class,
                'multiple' => true,
                'expanded' => false,
                // 'choice_label' => '',
                'label' => 'Sélectionnez un jeu :',
                'attr' => [
                    'placeholder' => 'Sélectionnez un jeu...'
                ]
            ])

            ->add('nomEquipe', TextType::class, [
                'label' => 'Nom de l\'équipe',
                'attr' => [
                    'placeholder' => 'Nom de l\'équipe'
                ]
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Logo de l\'équipe',
                'required' => false,
                'download_label' => 'download_file',
                'download_uri' => true,
                'image_uri' => true,
                'asset_helper' => true,
            ])
            // ->add('description', TextType::class, [
            //     'label' => 'Description de l\'équipe',
            //     'attr' => [
            //         'placeholder' => 'Description de l\'équipe'
            //     ]
            // ])

            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter',
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
