<?php

namespace App\Controller;

use App\Entity\Form;
use App\Entity\Answer;
use Symfony\Component\Mime\Email;
use App\Repository\FormRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class FormController extends AbstractController
{
    private $entityManager;
    private $formRepository;
    private $mailer;
    public function __construct(EntityManagerInterface $entityManager, FormRepository $formRepository, MailerInterface $mailer)
    {
        $this->entityManager = $entityManager;
        $this->formRepository = $formRepository;
        $this->mailer = $mailer;
    }

    #[Route('/formulaire', name: 'app_form')]
    public function index()
    {
        return $this->redirectToRoute('app_home');
    }

    #[Route('/formulaire/{slug}', name: 'app_form_slug')]
    public function inscriptionForm(string $slug, Request $request): Response
    {
        $form = $this->formRepository->findOneBy(['slug' => $slug]);

        if (!$form || !$form->isActive()) {
            throw $this->createNotFoundException('Formulaire non trouvé');
        }

        if ($request->isMethod('POST')) {
            $data = [];

            foreach ($form->getFields() as $field) {
                $name = $field->getName();
                $value = $request->request->all()[$name] ?? null;
                $data[$name] = $value;
            }

            $answer = new Answer();
            $answer->setForm($form);
            $answer->setData($data);

            $this->entityManager->persist($answer);
            $this->entityManager->flush();

            return $this->render('form/confirmation.html.twig', [
                'formConfig' => $form,
            ]);
        }

        return $this->render('form/index.html.twig', [
            'controller_name' => 'FormController',
            'formConfig' => $form,
            'fields' => $form->getFields(),
        ]);
    }

    #[Route('/test-email-send', name: 'app_member_test_email_send')]
    public function testEmailSend(): Response
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $email = (new Email())
            ->from('graphisme@univ-reunion.fr')
            ->to('setheve@viceversa.re')
            ->subject('Test envoi SMTP Gmail')
            ->text('Ceci est un test d’envoi SMTP.')
            ->html('<p>Ceci est un <strong>test</strong> d’envoi SMTP Gmail.</p>');

        $this->mailer->send($email);

        return new Response('Email envoyé');

    }

    public function sendMailAfterFormPostPublic(Answer $answer, Form $form, string $emailRecept): void
    {
        $objetMail = $form->getObjectEmail() ?? 'Votre inscription à ' . $form->getTitle();

        $email = (new TemplatedEmail())
            ->from('graphisme@univ-reunion.fr')
            ->to($emailRecept)
            ->subject($objetMail)
            ->htmlTemplate('email/inscription_qrcode.html.twig')
            ->context([
                'answer' => $answer,
            ]);

        $this->mailer->send($email);
    }

    public function errorSendMail(Answer $answer, Form $form): void
    {
        $email = (new TemplatedEmail())
            ->from('graphisme@univ-reunion.fr')
            ->to("setheve@viceversa.re")
            ->subject("Erreur d'envoi de mail depuis un formulaire public")
            ->htmlTemplate('email/error_send_mail.html.twig')
            ->context([
                'answer' => $answer,
                'form' => $form,
            ]);

        $this->mailer->send($email);
    }
}
