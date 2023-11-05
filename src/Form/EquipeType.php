<?php

namespace App\Form;

use App\Entity\Equipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomEquipe', TextType::class, [
                'label' => 'Nom de l\'équipe',
                'attr' => [
                    'placeholder' => 'Nom de l\'équipe'
                ]
            ])
            ->add('logo', TextType::class, [
                'label' => 'Logo de l\'équipe',
                'attr' => [
                    'placeholder' => 'Logo de l\'équipe'
                ]
            ])
            ->add('description', TextType::class, [
                'label' => 'Description de l\'équipe',
                'attr' => [
                    'placeholder' => 'Description de l\'équipe'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}
