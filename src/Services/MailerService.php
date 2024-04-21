<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\ContactDTO;
use App\Entity\Invitation;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

readonly class MailerService
{
    public function __construct(
        #[Autowire('%support_email%')] private string $supportEmail,
        private Security $security,
        private MailerInterface $mailer,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * @throws \Exception|TransportExceptionInterface
     */
    public function contact(FormInterface $form): void
    {
        $data = $form->getData();

        $date = new \DateTimeImmutable(datetime: 'now', timezone: new \DateTimeZone('Europe/Paris'));
        $formattedDate = $date->format(format: 'd/m/Y \à H:i');

        if ($data instanceof ContactDTO) {
            $email = (new TemplatedEmail())
                ->from(from: $this->supportEmail)
                ->to(to: $this->supportEmail)
                ->subject(subject: $data->subject)
                ->htmlTemplate(template: 'emails/contact.html.twig')
                ->context(context: [
                    'name' => $data->name,
                    'subject' => $data->subject,
                    'contact' => $data->email,
                    'message' => $data->message,
                    'date' => $formattedDate,
                ]);

            $this->mailer->send($email);
        }
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[IsGranted('ROLE_USER')]
    public function sendWelcomeEmail(): void
    {
        $fullname = $this->getFullnameUserConnected();

        if (null !== $fullname) {
            $email = (new NotificationEmail())
                ->from($this->supportEmail)
                ->subject(subject: 'Bienvenue sur le site Eterno')
                ->htmlTemplate(template: 'emails/welcome.html.twig')
                ->to($this->supportEmail)
                ->context(context: [
                    'username' => $fullname,
                ]);

            $this->mailer->send($email);
        }
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendInvitationEmail(
        FormInterface $form
    ): void {
        $data = $form->getData();
        /** @phpstan-ignore-next-line */
        $uuid = $form->getData()->getUuid();

        if ($data instanceof Invitation) {
            // The token is the uuid of the invitation
            if (null !== $uuid) {
                $token = $uuid->toRfc4122();
                $email = (new NotificationEmail())
                    ->from($this->supportEmail)
                    ->subject(subject: 'Invitation à rejoindre Eterno')
                    ->htmlTemplate(template: 'emails/invitation.html.twig')
                    /* @phpstan-ignore-next-line */
                    ->to($form->getData()->getEmail())
                    ->context(context: [
                        'parrain' => $this->getFullnameUserConnected(),
                        'token' => $token,
                        'url' => $this->urlGenerator->generate(
                            name: 'invitation.verify',
                            parameters: ['uuid' => $token],
                            referenceType: UrlGeneratorInterface::ABSOLUTE_URL
                        ),
                    ]);

                $this->mailer->send($email);
            }
        }
    }

    /**
     * @description: Récupère l'utilisateur connecté
     */
    private function getUser(): ?User
    {
        if (!$this->security->getUser() instanceof User) {
            return null;
        }

        return $this->security->getUser();
    }

    protected function getFullnameUserConnected(): ?string
    {
        return $this->getUser() ? $this->getUser()->getFullname() : null;
    }
}
