<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\Mime\Email;
use App\Event\ContactRequestEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

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
            ->to($data->service)
            ->from($data->email)    //l'email saisi par l'U
            ->subject('Demande de contact')
            ->htmlTemplate('emails/contact.html.twig')
            ->context(['data' => $data]);
            //envoi de l'e-mail
        $this->mailer->send($mail);
    }

    //Envoie un e-mail à l'utilisateur qui se connecte
    public function onLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        if (!$user instanceof User) {
            return;
        }
        $mail = (new Email())
        ->to($user->getEmail())
        ->from('support@demo.fr')
        ->subject('Connexion')
        ->text('Vous vous êtes connecté*e');
    //envoi de l'e-mail
    $this->mailer->send($mail);

    }


    public static function getSubscribedEvents(): array
    {
        return [
            ContactRequestEvent::class => 'onContactRequestEvent',
            //événement appelé lorsque l'utilisateur se connecte via le formulaire plutôt que via simple méthode
            InteractiveLoginEvent::class => 'onLogin',
        ];
    }
}
