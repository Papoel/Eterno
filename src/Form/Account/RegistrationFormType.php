<?php

namespace App\Form\Account;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'firstname', type: TextType::class, options: [
                'row_attr' => [
                    'class' => 'col-sm-6 col-lg-4',
                ],
                'label' => 'Prénom',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control text-capitalize',
                    'id' => 'profile_firstname',
                ],
                'required' => false,
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner votre prénom.'),
                    new NotNull(message: 'Veuillez renseigner votre prénom.'),
                ],
            ])

            ->add(child: 'email', type: EmailType::class, options: [
                'row_attr' => [
                    'class' => 'col-12',
                ],
                'label' => 'Email',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control col-12',
                    'placeholder' => 'Email',
                ],
            ])

            ->add(child: 'password', type: RepeatedType::class, options: [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                    'attr' => [
                        'class' => 'form-control password',
                        'placeholder' => 'Mot de passe',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                    'attr' => [
                        'class' => 'form-control password',
                        'placeholder' => 'Confirmer le mot de passe',
                    ],
                ],
                'invalid_message' => 'Les mots de passe doivent être identiques.',
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner un mot de passe.'),
                    new Length(
                        min: 6,
                        minMessage: 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
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
