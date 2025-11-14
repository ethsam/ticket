<?php

namespace App\Controller\Admin;

use App\Repository\FormRepository;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private $formRepository;

    public function __construct(FormRepository $formRepository)
    {
        $this->formRepository = $formRepository;
    }

    public function index(): Response
    {
        $forms = $this->formRepository->findBy([], ['id' => 'DESC']);
        return $this->render('admin/dashboard/welcome.html.twig', [
            'forms' => $forms
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Ticketing');
    }

    public function configureMenuItems(): iterable
    {
        if ( $this->isGranted('ROLE_ADMIN') ) {

            return [
                yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),

                yield MenuItem::section('GESTIONS'),
                yield MenuItem::linkToCrud('Formulaires', 'fas fa-file-alt', \App\Entity\Form::class)->setController(FormCrudController::class),

                yield MenuItem::section('PARAMETRES'),
                yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', \App\Entity\User::class)->setController(UserCrudController::class),

                yield MenuItem::section('', ''),
                yield MenuItem::linkToUrl('ticket.re', 'fas fa-at', 'https://www.ticket.re')->setLinkTarget('_blank'),
            ];

        } else {
            // return $this->redirect('/');

            return [
                yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),

                yield MenuItem::section('GESTIONS'),
                yield MenuItem::linkToCrud('Formulaires', 'fas fa-file-alt', \App\Entity\Form::class)->setController(FormCrudController::class),

                yield MenuItem::section('DATAS'),
                yield MenuItem::linkToCrud('Réponses formulaires', 'fas fa-check-circle', \App\Entity\Answer::class)->setController(AnswerCrudController::class),

                yield MenuItem::section('PARAMETRES'),
                yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', \App\Entity\User::class)->setController(UserCrudController::class),

                yield MenuItem::section('', ''),
                yield MenuItem::linkToUrl('ticket.re', 'fas fa-at', 'https://www.ticket.re')->setLinkTarget('_blank'),
            ];
        }
    }
}
