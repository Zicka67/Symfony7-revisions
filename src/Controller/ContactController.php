<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $data = new ContactDTO();

        // $data->name = 'John';
        // $data->email = 'John@gmail.com';
        // $data->message = 'dqzdqdqzd';

        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);

        // Si le form est envoyé et valide 
        if ($form->isSubmitted() && $form->isValid()) {

            // Envoyer l'e-mail avec ces données
            try {
            $mail = (new TemplatedEmail())
                ->to($data->service) 
                ->from($data->email)
                // ->replyTo($formData['email'])
                ->subject('Nouveau message du formulaire de contact')
                ->htmlTemplate('emails/contact.html.twig')
                ->context(['data' => $data]);
                    $mailer->send($mail);

                // Ajouter un message flash de succès
                $this->addFlash('success', 'Le message a été envoyé avec succès.');
                return $this->redirectToRoute('home');

            } catch (\Exception $e) {
                $this->addFlash('danger', 'Impossible d\'envoyer votre email');
            }
    
            $mailer->send($mail);

        }
        return $this->render('contact/contact.html.twig', [
            'form' => $form
        ]);
    }


}
