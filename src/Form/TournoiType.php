<?php

namespace App\Form;

use App\Entity\Jeu;
use App\Entity\Tournoi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class TournoiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomTournoi', TextType::class, [
                'label' => 'Titre * ',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Tournoi de League of Legends, ...',
                    'class' => 'nomTournoi'
                ]
            ])
            ->add('nomOrganisation', TextType::class, [
                'label' => 'Nom de l\'organisation * ',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Riot Games, Blizzard, ...',
                    'class' => 'nomOrga'
                ]
            ])
            ->add('dateDebut', DateTimeType::class, [
                'input' => 'datetime_immutable',
                'label' => 'Date de début * ',
                'required' => true,
                'widget' => 'single_text',
                'attr'   => [
                    'min' => (new \DateTime())->format('Y-m-d H:i'),
                    'class' => 'dateDebut'
                    ]
            ])
            ->add('dateFin', DateTimeType::class, [
                'input' => 'datetime_immutable',
                'label' => 'Date de fin * ',
                'required' => true,
                'widget' => 'single_text',
                'attr'   => [
                    'min' => (new \DateTime())->format('Y-m-d H:i'),
                    'class' => 'dateFin'
                    ]

            ])
            // NbJoueursMax
            ->add('nbJoueursMax', IntegerType::class, [
                'label' => 'Nombre de joueurs maximum * ',
                'required' => true,
                'attr' => [
                    'min' => 1, 
                    'max' => 1000, 
                    'step' => 1,
                    'class' => 'nombreJoueursMax',
                    'placeholder' => 'Autorisé entre 1 et 1000 joueurs.'
                ],
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'max' => 1000,
                        'minMessage' => 'Le nombre de joueurs doit être au moins de {{ limit }}.',
                        'maxMessage' => 'Le nombre de joueurs ne peut pas dépasser {{ limit }}.'
                    ]),
                ],
            ])
            ->add('logoFile', VichImageType::class, [
                'label' => 'Logo du tournoi *: ',
                'required' => true,
                'attr' => [
                    'class' => 'logoTournoi'
                ]

            ])
            ->add('banniereTrFile', VichImageType::class, [
                'label' => 'Bannière *: ',
                'required' => true,
                'attr' => [
                    'class' => 'banniereTournoi'
                ]
            ])
            ->add('region', TextType::class, [
                'label' => 'Région *: ',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ex: Europe, Amérique du Nord, ...',
                    'class' => 'regionTournoi'
                ]
            ])
            ->add('lienTwitch', TextType::class, [
                'label' => 'Lien Twitch : ',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: https://www.twitch.tv/...',
                    'class' => 'lienTwitch'
                ]
            ])
            ->add('jeu', EntityType::class, [
                'class' => Jeu::class,
                'choice_label' => 'nomJeu',
                'label' => 'Jeu',
                'placeholder' => 'Choisissez un jeu *: ',
                'attr' => [
                    'class' => 'jeuTournoi'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tournoi::class,
        ]);
    }
}
