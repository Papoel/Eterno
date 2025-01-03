<?php

namespace App\Form\Light;

use App\Entity\Light;
use App\EventSubscriber\PictureLightSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

class LightType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'photoFile', type: VichImageType::class, options: [
                'label' => false,
                'required' => false,
                'download_uri' => false,
                'image_uri' => false,
                'allow_delete' => true,
                'delete_label' => 'Cocher pour supprimer, puis sauvegarder',
            ])
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
                'required' => false,
            ])
            ->add(child: 'birthdayAt', type: DateType::class, options: [
                'row_attr' => ['class' => 'col-sm-6 col-lg-4 my-3'],
                'label' => false,
                'label_attr' => ['class' => 'form-label'],
                'attr' => [
                    'class' => 'form-control flatpickr',
                    'id' => 'light_birthday',
                ],
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add(child: 'deceasedAt', type: DateType::class, options: [
                'row_attr' => ['class' => 'col-sm-6 col-lg-4 my-3'],
                'label' => false,
                'label_attr' => ['class' => 'form-label'],
                'attr' => [
                    'class' => 'form-control flatpickr',
                    'id' => 'light_deceased',
                ],
                'widget' => 'single_text',
                'required' => false,
                'constraints' => [
                    new Callback(callback: function ($object, ExecutionContextInterface $context) {
                        $today = new \DateTime();

                        if ($object > $today) {
                            $context->buildViolation(message: 'La date de décès ne peut pas être supérieure à la date du jour.')
                                 ->atPath(path: 'deceasedAt')
                                 ->addViolation();
                        }
                    }),
                ],
            ])
            ->addEventSubscriber(subscriber: new PictureLightSubscriber())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Light::class]);
    }
}
