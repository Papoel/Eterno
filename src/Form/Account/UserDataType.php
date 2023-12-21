<?php

declare(strict_types=1);

namespace App\Form\Account;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class UserDataType extends AbstractType
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
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner votre prénom.'),
                    new NotNull(message: 'Veuillez renseigner votre prénom.'),
                ],
            ])
            ->add(child: 'lastname', type: TextType::class, options: [
                'row_attr' => [
                    'class' => 'col-sm-6 col-lg-4',
                ],
                'label' => 'Nom',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control text-capitalize',
                    'id' => 'profile_lastname',
                ],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner votre nom.'),
                    new NotNull(message: 'Veuillez renseigner votre nom.'),
                ],
            ])
            ->add(child: 'username', type: TextType::class, options: [
                'row_attr' => [
                    'class' => 'col-sm-6',
                ],
                'label' => 'Pseudo',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'profile_username',
                ],
            ])
            ->add(child: 'birthday', type: DateType::class, options: [
                'row_attr' => [
                    'class' => 'col-sm-6 col-lg-4',
                ],
                'label' => 'Date de naissance',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control flatpickr',
                    'id' => 'profile_birthday',
                ],
                'widget' => 'single_text',
            ])
            ->add(child: 'mobile', type: TelType::class, options: [
                'row_attr' => [
                    'class' => 'col-sm-6 col-lg-4',
                ],
                'label' => 'Numéro de téléphone',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'profile_mobile',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => User::class]);
    }
}
