<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
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
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'Ton adresse email *',
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'exemple@exemple.com',
                        'aria-label' => 'Adresse email'
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Entrez une adresse email',
                        ]),

                        new Regex([
                            'pattern' => '/^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$/',
                            'message' => 'Le format de l\'adresse email est invalide.'
                        ]),
                    ],
                ]
            )
            ->add(
                'pseudo',
                TextType::class,
                [
                    'label' => 'Ton pseudo *',
                    'required' => true,
                    'attr' => [
                        'class' => 'form-pseudo',
                        'placeholder' => 'Pseudo',
                        'aria-label' => 'Pseudo'
                    ]
                ]
            )
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Les champs du mot de passe doivent correspondre.',
                'required' => true,
                'first_options'  => [
                    'label' => 'Ton mot de passe *',
                    'attr' => [
                        'class' => 'password-field',
                        'placeholder' => '••••••••',
                        'aria-label' => 'Mot de passe'
                    ]
                ],
                'second_options' => [
                    'label' => 'Répète ton mot de passe *',
                    'attr' => [
                        'class' => 'password-field',
                        'placeholder' => '••••••••',
                        'aria-label' => 'Confirmation du mot de passe'
                    ]
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez un mot de passe',
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{12,}$/',
                        'message' => 'Votre mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial'
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'attr' => [
                    'class' => 'form-check-input',
                    'aria-label' => 'Accepter les conditions d\'utilisation'
                ],
                'required' => true,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions générales d\'utilisation.',
                    ]),
                ],
                'label' => 'J\'accepte les conditions d\'utilisation *',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
