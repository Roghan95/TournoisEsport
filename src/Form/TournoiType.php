<?php

namespace App\Form;

use App\Entity\Jeu;
use App\Entity\Tournoi;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

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
            ->add('dateDebut', DateType::class, [
                'input' => 'datetime_immutable',
                'label' => 'Date de début * ',
                'required' => true
            ])
            ->add('dateFin', DateType::class, [
                'input' => 'datetime_immutable',
                'label' => 'Date de fin * ',
                'required' => true
            ])
            ->add('nbJoueurMax', IntegerType::class, [
                'label' => 'Nombre de joueurs max *',
                'required' => true
            ])
            ->add(
                'description',
                CKEditorType::class,
                [
                    'label' => 'Description *: ',
                    'required' => true
                ]
            )
            ->add('logoFile', VichImageType::class, [
                'label' => 'Logo du tournoi *: ',
                'required' => true,

            ])
            ->add('banniereTrFile', VichImageType::class, [
                'label' => 'Bannière *: ',
                'required' => true
            ])
            ->add('lienTwitch', TextType::class, [
                'label' => 'Lien Twitch : ',
                'required' => false
            ])

            ->add('jeu', EntityType::class, [
                'class' => Jeu::class,
                'choice_label' => 'nomJeu',
                'label' => 'Jeu',
                'placeholder' => 'Choisir un jeu *: '
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tournoi::class,
        ]);
    }
}
