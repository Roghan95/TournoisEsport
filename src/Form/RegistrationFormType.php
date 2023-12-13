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
                        'aria-label' => 'Adresse email' // Ajout de l'aria-label pour l'accessibilité
                    ]
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
                        'aria-label' => 'Pseudo' // Ajout de l'aria-label pour l'accessibilité
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
                        'aria-label' => 'Mot de passe' // Ajout de l'aria-label pour l'accessibilité
                    ]
                ],
                'second_options' => [
                    'label' => 'Répète ton mot de passe *',
                    'attr' => [
                        'class' => 'password-field',
                        'placeholder' => '••••••••',
                        'aria-label' => 'Confirmation du mot de passe' // Ajout de l'aria-label pour l'accessibilité
                    ]
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez un mot de passe',
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,}$/',
                        'message' => 'Le mot de passe doit contenir au moins 12 caractères, incluant une majuscule, une minuscule, un chiffre et un caractère spécial.'
                    ])
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'attr' => [
                    'class' => 'form-check-input',
                    'aria-label' => 'Accepter les conditions d\'utilisation' // Ajout de l'aria-label pour l'accessibilité
                ],
                'required' => true,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions générales d\'utilisation.',
                    ]),
                ],
                'label' => 'J’accepte les conditions d\'utilisation *',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
