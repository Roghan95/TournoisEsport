<?php

namespace App\Form;

use App\Entity\GameMatch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class GameMatchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('typeMatch', ChoiceType::class, [
                'choices' => [
                    'Solo' => 'Solo',
                    'Duo' => 'Duo',
                    'Trio' => 'Trio',
                    '5v5' => '5v5',
                ],
                'label' => 'Type de match : ',
            ])
            ->add(
                'dateDebut',
                DateTimeType::class,
                [
                    'label' => 'Date de début : ',
                    'required' => true
                ]
            )
            ->add(
                'nbJoueursMax',
                ChoiceType::class,
                [
                    'choices' => [
                        '5' => '10',
                        '10' => '50',
                        '50' => '100',
                        '100' => '150',
                    ],
                    'label' => 'Nombre de joueurs maximum : ',
                    'required' => true
                ]
            )
            ->add('submit', SubmitType::class, [
                'label' => 'Créer le match',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GameMatch::class,
        ]);
    }
}
