<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FormController extends AbstractController
{
    #[Route('/inscription', name: 'app_form')]
    public function index()
    {
        return $this->redirectToRoute('app_home');
    }

    #[Route('/inscription/{id}', name: 'app_form_id')]
    public function inscriptionForm(int $id): Response
    {
        return $this->render('form/index.html.twig', [
            'controller_name' => 'FormController',
        ]);
    }
}
