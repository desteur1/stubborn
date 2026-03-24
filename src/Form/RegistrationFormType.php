<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
// pour les champs de texte
use Symfony\Component\Form\Extension\Core\Type\TextType;
// pour les champs de mot de passe
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
// pour les champs d'
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class, [
                'label' => 'Nom utilisateur',
                'constraints' => [
                    new NotBlank(
                        message: 'le nom est obligatoire'
                    ),
                    new Length(
                        min: 3,
                        minMessage: 'le nom doit comporter au moins {{ limit }} caractères',
                        max: 50,
                        maxMessage: 'le nom ne doit pas dépasser {{ limit }} caractères'
                    )
                    
                    
                ]
                        
                ])

            ->add('email', EmailType::class, [
                'label' => ' Email',
                'constraints' => [
                    
                    new NotBlank(
                        message: 'l\'email est obligatoire'
                    ),
                    new Length(
                        min: 5,
                        minMessage: 'l\'email doit comporter au moins {{ limit }} caractères',
                        max: 180,
                        maxMessage: 'l\'email ne doit pas dépasser {{ limit }} caractères'
                    )
                ]
                ])

            ->add('deliveryAddress', TextType::class, [
                'label' => 'Adresse de livraison',
                'required' => false,
            ])

            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmer le mot de passe'],
                'invalid_message' => 'Les mots de passe ne correspondent pas',
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue(
                        message: 'vous devez accepter les conditions.',
                    ),
                ],
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
