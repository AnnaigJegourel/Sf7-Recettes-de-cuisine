<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $data = new ContactDTO;

        //Donner de test, à supprimer
        $data->name = 'Jane Doe';
        $data->email = 'jane@doe.be';
        $data->message = 'Super site';
        
        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            try {
                //Création de l'email avec un template Twig
                $mail = (new TemplatedEmail())
                    ->to($data->service)
                    //->to('ae') // pour tester l'erreur d'envoi
                    ->from($data->email)    //l'email saisi par l'U
                    ->subject('Demande de contact')
                    ->htmlTemplate('emails/contact.html.twig')
                    ->context(['data' => $data]);
                //Symfony\Component\Mime\Email avec du html
                //$mail = (new Email())

                //envoi de l'e-mail
                $mailer->send($mail);

                $this->addFlash('success', "Votre e-mail a bien été envoyé");
                
                return $this->redirectToRoute('contact');

            } catch (\Exception $e) {
                $this->addFlash('danger', "Impossible d'envoyer votre e-mail");
            }
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form,
        ]);
    }
}
