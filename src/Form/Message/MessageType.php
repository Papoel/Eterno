<?php

namespace App\Form\Message;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'content', type: TextareaType::class, options: [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Écrivez votre message ici...',
                    'rows' => 3,
                    'cols' => 0,
                    'class' => 'form-control mb-0',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
