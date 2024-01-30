<?php

namespace App\Form;

use App\Entity\GameMatch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class GameMatchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomMatch', TextType::class, [
                'label' => 'Nom du match * ',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Exemple : Match de League of Legends 5v5',
                    'class' => 'nomMatch'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un nom de match',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le nom du match doit contenir au moins {{ limit }} caractères',
                        'max' => 30,
                        'maxMessage' => 'Le nom du match ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
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
