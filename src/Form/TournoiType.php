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
                'required' => true
            ])
            ->add('nomOrganisation', TextType::class, [
                'label' => 'Nom de l\'organisation * ',
                'required' => true
            ])
            ->add('dateDebut', DateTimeType::class, [
                'input' => 'datetime_immutable',
                'label' => 'Date de début * ',
                'required' => true,
                'widget' => 'single_text',
                'attr'   => ['min' => (new \DateTime())->format('Y-m-d H:i')]
            ])
            ->add('dateFin', DateTimeType::class, [
                'input' => 'datetime_immutable',
                'label' => 'Date de fin * ',
                'required' => true,
                'widget' => 'single_text',
                'attr'   => ['min' => (new \DateTime())->format('Y-m-d H:i')]
            ])
            // NbJoueursMax
            ->add('nbJoueursMax', IntegerType::class, [
                'label' => 'Nombre de joueurs maximum * ',
                'required' => true,
                'attr' => [
                    'min' => 1, // Limite minimale (ajustez selon vos besoins)
                    'max' => 100, // Limite maximale (ajustez selon vos besoins)
                    'step' => 1, // Incrément - 1 pour les nombres entiers
                    'class' => 'custom-class', // Classe CSS pour le style personnalisé
                    'placeholder' => 'Nombre maximum de joueurs'
                ],
                'constraints' => [
                    // new NotBlank([
                    //     'message' => 'Veuillez entrer le nombre de joueurs maximum.'
                    // ]),
                    new Range([
                        'min' => 1,
                        'max' => 100,
                        'minMessage' => 'Le nombre de joueurs doit être au moins de {{ limit }}.',
                        'maxMessage' => 'Le nombre de joueurs ne peut pas dépasser {{ limit }}.'
                    ]),
                ],
            ])
            ->add('logoFile', VichImageType::class, [
                'label' => 'Logo du tournoi *: ',
                'required' => true,

            ])
            ->add('banniereTrFile', VichImageType::class, [
                'label' => 'Bannière *: ',
                'required' => true
            ])
            ->add('region', TextType::class, [
                'label' => 'Région *: ',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ex: Europe, Amérique du Nord, ...'
                ]
            ])
            ->add('lienTwitch', TextType::class, [
                'label' => 'Lien Twitch : ',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: https://www.twitch.tv/...'
                ]
            ])
            ->add('jeu', EntityType::class, [
                'class' => Jeu::class,
                'choice_label' => 'nomJeu',
                'label' => 'Jeu',
                'placeholder' => 'Choisissez un jeu *: '
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tournoi::class,
        ]);
    }
}
