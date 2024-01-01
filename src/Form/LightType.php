<?php

namespace App\Form;

use App\Entity\Light;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class LightType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'firstname', type: TextType::class, options: [
                'row_attr' => [
                    'class' => 'rounded-0',
                ],
                'label' => false,
                'attr' => [
                    'class' => 'form-control text-capitalize rounded-0',
                ],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner votre prénom.'),
                    new NotNull(message: 'Veuillez renseigner votre prénom.'),
                ],
            ])
            ->add(child: 'lastname', type: TextType::class, options: [
                'row_attr' => [
                    'class' => '',
                ],
                'label' => false,
                'attr' => [
                    'class' => 'form-control text-capitalize rounded-0',
                ],
            ])
            ->add(child: 'username', type: TextType::class, options: [
                'row_attr' => [
                    'class' => 'col-6',
                ],
                'label' => false,
                'attr' => [
                    'class' => 'form-control col-6',
                ],
            ])
            ->add(child: 'birthdayAt', type: DateType::class, options: [
                'row_attr' => [
                    'class' => 'col-sm-6 col-lg-4 my-3',
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
            ->add(child: 'deceasedAt', type: DateType::class, options: [
                'row_attr' => [
                    'class' => 'col-sm-6 col-lg-4 my-3',
                ],
                'label' => false,
                'attr' => [
                    'class' => 'form-control flatpickr',
                    'id' => 'profile_birthday',
                ],
                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Light::class,
        ]);
    }
}
