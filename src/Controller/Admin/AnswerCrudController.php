<?php

namespace App\Controller\Admin;

use App\Entity\Answer;
use Symfony\Component\Routing\RouterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AnswerCrudController extends AbstractCrudController
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public static function getEntityFqcn(): string
    {
        return Answer::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        yield DateTimeField::new('createdAt', 'Soumis le')
            ->setFormat('dd/MM/yyyy HH:mm')
            ->onlyOnIndex();

        yield TextField::new('form', 'Titre du formulaire')
            ->onlyOnIndex();

        yield TextField::new('firstDataValue', 'Donnée')
            ->onlyOnIndex()
            ->setSortable(false);

        // Affichage brut du JSON formaté
        yield ArrayField::new('data', 'Données')
            ->onlyOnForms()
            ->setTemplatePath('admin/components/answer_data.html.twig');

        yield BooleanField::new('validate', 'Validé');

        yield DateTimeField::new('validateAt', 'Validé le')
                ->setDisabled(true)
                ->hideOnIndex();

    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Réponses')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Détail d’une réponse')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        $export = Action::new('exportAnswers', 'Exporter')
            ->displayAsLink()  // force l'affichage
            ->setIcon('fa fa-file-excel')
            ->linkToRoute('admin_answers_export', function (Answer $answer) {
                return [
                    'formId' => $answer->getForm()->getId()
                ];
            });

        return $actions
            ->add(Crud::PAGE_INDEX, $export);
    }

}
