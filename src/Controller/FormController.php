<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Repository\FormRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class FormController extends AbstractController
{
    private $entityManager;
    private $formRepository;

    public function __construct(EntityManagerInterface $entityManager, FormRepository $formRepository)
    {
        $this->entityManager = $entityManager;
        $this->formRepository = $formRepository;
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
}
