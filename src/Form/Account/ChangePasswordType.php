<?php

declare(strict_types=1);

namespace App\Form\Account;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'currentPassword', type: PasswordType::class, options: [
                'row_attr' => [
                    'class' => 'col-12',
                ],
                'label' => 'Mot de passe actuel',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control password',
                    'placeholder' => 'Mot de passe actuel',
                ],
                'mapped' => false,
            ])
            ->add(child: 'newPassword', type: RepeatedType::class, options: [
                'type' => PasswordType::class,
                'required' => true,
                'mapped' => false,
                'first_options' => [
                    'label' => 'Nouveau mot de passe',
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                    'attr' => [
                        'class' => 'form-control password',
                        'placeholder' => 'Nouveau mot de passe',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer le nouveau mot de passe',
                    'label_attr' => [
                        'class' => 'form-label mt-2',
                    ],
                    'attr' => [
                        'class' => 'form-control password',
                        'placeholder' => 'Confirmer le nouveau mot de passe',
                    ],
                ],
                'invalid_message' => 'Les mots de passe doivent être identiques.',
                'constraints' => [
                    new NotBlank(),
                    new Length(min: 8),
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
