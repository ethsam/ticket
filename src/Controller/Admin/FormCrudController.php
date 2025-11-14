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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FormCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Form::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        yield FormField::addPanel('Informations générales')->setColumns(6);
        yield TextField::new('slug', 'Identifiant du formulaire')->setColumns(6);
        yield BooleanField::new('active', 'Actif ?')->setColumns(6);

        yield TextareaField::new('description', 'Description')->hideOnIndex()->setColumns(6);
        yield TextareaField::new('confirmationMessage', 'Message de confirmation')->hideOnIndex()->setColumns(6);

        yield FormField::addPanel('Champs du formulaire');

        yield CollectionField::new('fields')
            ->setLabel('Champs')
            ->hideOnIndex()
            ->useEntryCrudForm(FieldCrudController::class)
            ->setColumns(6);

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
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE); // perso : on supprime rarement les formulaires
    }
}
