<?php

declare(strict_types=1);

namespace App\Form\Account;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class AvatarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            child: 'avatarFile',
            type: VichFileType::class,
            options: [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'allow_delete' => false,
                'download_uri' => false,
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'user' => User::class,
            // 🚥 commenter pour réactiver la validation html5 !
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
