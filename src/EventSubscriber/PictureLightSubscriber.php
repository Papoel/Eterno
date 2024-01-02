<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Light;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class PictureLightSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SUBMIT => ['onPostSubmit'],
        ];
    }

    public function onPostSubmit(FormEvent $event): void
    {
        $this->setDefaultPicture($event);
    }

    /**
     * @throws \Exception
     */
    // Si l'entité est une instance de Light et que la photo est null alors, on lui attribue une photo par défaut.
    public function setDefaultPicture(FormEvent $event): void
    {
        $formLight = $event->getData();

        if (!$formLight instanceof Light) {
            return;
        }

        if (null === $formLight->getPhoto()) {
            $formLight->setPhoto(photo: 'default.jpg');
        }
    }
}
