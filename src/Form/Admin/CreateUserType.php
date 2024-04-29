<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'firstname', type: TextType::class)
            ->add(child: 'lastname', type: TextType::class)
            ->add(child: 'email', type: EmailType::class)
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
            // Select Roles with buttons radio
            ->add(child: 'roles', type: ChoiceType::class, options: [
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'expanded' => false,
                'multiple' => true,
                'label' => 'Rôles',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(defaults: [
            'data_class' => User::class,
        ]);
    }
}
