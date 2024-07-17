<?php

namespace App\EventSubscriber;

use App\Event\ContactRequestEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;

class MailingSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MailerInterface $mailer)
    {
        
    }


    public function onContactRequestEvent(ContactRequestEvent $event): void
    {
        //ici la logique d'envoi d'e-mails qui était dans le ContactController
        $data = $event->data;
        //Création de l'email avec un template Twig
        $mail = (new TemplatedEmail())
            ->to('ae') // pour tester l'erreur d'envoi
            ->from($data->email)    //l'email saisi par l'U
            ->subject('Demande de contact')
            ->htmlTemplate('emails/contact.html.twig')
            ->context(['data' => $data]);
        //envoi de l'e-mail
        $this->mailer->send($mail);
    }


    public static function getSubscribedEvents(): array
    {
        return [
            ContactRequestEvent::class => 'onContactRequestEvent',
        ];
    }
}
