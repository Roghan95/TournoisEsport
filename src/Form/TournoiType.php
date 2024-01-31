<?php

namespace App\Form;

use App\Entity\Jeu;
use App\Entity\Tournoi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class TournoiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomTournoi', TextType::class, [
                'label' => 'Titre *: ',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Tournoi de League of Legends, ...',
                    'class' => 'nomTournoi'
                ]
            ])
            ->add('nomOrganisation', TextType::class, [
                'label' => 'Nom de l\'organisation *: ',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Riot Games, Blizzard, ...',
                    'class' => 'nomOrga'
                ]
            ])
            ->add('dateDebut', DateTimeType::class, [
                'input' => 'datetime_immutable',
                'label' => 'Date de début *: ',
                'required' => true,
                'widget' => 'single_text',
                'attr'   => [
                    'min' => (new \DateTime())->format('Y-m-d H:i'),
                    'class' => 'dateDebut'
                ]
            ])
            ->add('dateFin', DateTimeType::class, [
                'input' => 'datetime_immutable',
                'label' => 'Date de fin *: ',
                'required' => true,
                'widget' => 'single_text',
                'attr'   => [
                    'min' => (new \DateTime())->format('Y-m-d H:i'),
                    'class' => 'dateFin'
                ]

            ])
            ->add('region', TextType::class, [
                'label' => 'Région : ',
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
            // NbJoueursMax
            ->add('nbJoueursMax', IntegerType::class, [
                'label' => 'Nombre de joueurs maximum *: ',
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
            ->add('jeu', EntityType::class, [
                'class' => Jeu::class,
                'choice_label' => 'nomJeu',
                'label' => 'Jeu *:',
                'placeholder' => 'Choisissez un jeu *: ',
                'attr' => [
                    'class' => 'jeuTournoi'
                ]
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Solo' => 'solo',
                    'Equipe' => 'equipe'
                ],
                'label' => 'Type de tournoi *: ',
                'required' => true,
                'attr' => [
                    'class' => 'typeTournoi'
                ]
            ])
            ->add('logoFile', VichImageType::class, [
                'label' => 'Logo du tournoi (5Mo max) *: ',
                'required' => true,
                'attr' => [
                    'class' => 'logoTournoi',
                    'placeholder' => 'jpg, png, jpeg, gif, webp',
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
            ->add('banniereTrFile', VichImageType::class, [
                'label' => 'Bannière (5Mo max) *: ',
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tournoi::class,
        ]);
    }
}
