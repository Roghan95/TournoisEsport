<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, 
        [
            'label' => 'Ton email *',
            'required' => true,
            'attr' => [
                'class' => 'form-control'
            ] 
        ])
            ->add('pseudo', TextType::class, 
            [
                'label' => 'Ton pseudo *',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])

            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
               'type' => PasswordType::class,
               'invalid_message' => 'Les champs du mot de passe doivent correspondre.',
                'options' => ['attr' => ['class' => 'password-field form-control']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe *'],
                'second_options' => ['label' => 'Confirme ton mot de passe *'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez un mot de passe',

                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Votre mot de passe doît avoir minimum {{ limit }} caractères',
                    //     // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{10,}$/',
                        'message' => 'Votre mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial'

                    ])
                ],
            ])


            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions générales d\'utilisation.',
                    ]),
                ],
                'label' => 'J\'accepte les conditions générales d\'utilisation *',
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
