<?php

namespace App\Controller\Admin;

use App\Entity\Form;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FormCrudController extends AbstractCrudController
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Form::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        yield FormField::addPanel('Informations générales')->setCssClass('col-sm-6');
        yield TextField::new('title', 'Titre du formulaire')->setColumns(12)->setRequired(true);
        yield SlugField::new('slug', 'Adresse du formulaire')->setTargetFieldName('title')->setColumns(12)->setRequired(true);

        yield TextareaField::new('description', 'Description')->hideOnIndex()->setColumns(12);
        yield TextareaField::new('confirmationMessage', 'Message de confirmation')->hideOnIndex()->setColumns(12);

        yield FormField::addPanel('Champs du formulaire');

        yield CollectionField::new('fields')
            ->setLabel('Champs')
            ->hideOnIndex()
            ->useEntryCrudForm(FieldCrudController::class)
            ->setColumns(6);

        yield FormField::addPanel('Visibilité');
        yield BooleanField::new('active', 'Formulaire en ligne')->setColumns(6);

        yield FormField::addPanel('Métadonnées');
        yield DateTimeField::new('createdAt', 'Créé le')->onlyOnDetail();
        yield DateTimeField::new('updatedAt', 'Modifié le')->onlyOnDetail();

    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Formulaires')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer un formulaire')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier un formulaire')
            ->setDefaultSort(['id' => 'DESC'])
            ->overrideTemplates([
                'crud/edit' => 'admin/components/crud_edit.html.twig',
                ])
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE); // perso : on supprime rarement les formulaires
    }
}
