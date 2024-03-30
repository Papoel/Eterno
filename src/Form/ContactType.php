<?php

namespace App\Form;

use App\DTO\ContactDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'name', type: TextType::class, options: [
                'empty_data' => '',
                'label' => 'Nom et prénom',
                'required' => true,
            ])
            ->add(child: 'subject', type: ChoiceType::class, options: [
                'choices' => [
                    'Création de compte' => 'creation de compte',
                    'Signalement de bug' => 'signalement de bug',
                    'Demande de renseignements' => 'demande de renseignements',
                    'Demande de fonctionnalité' => 'demande de functionality',
                    'Autre' => 'Autre',
                ],
            ])
            ->add(child: 'email', type: EmailType::class, options: [
                'empty_data' => '',
                'label' => 'Email',
            ])
            ->add(child: 'message', type: TextareaType::class, options: [
                'empty_data' => '',
                'label' => 'Message',
                'attr' => ['rows' => 5],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactDTO::class,
            // html5 validation
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
