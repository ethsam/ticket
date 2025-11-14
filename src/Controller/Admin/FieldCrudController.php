<?php

namespace App\Controller\Admin;

use App\Entity\Field;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Component\String\Slugger\SluggerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class FieldCrudController extends AbstractCrudController
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public static function getEntityFqcn(): string
    {
        return Field::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $fieldTypes = [
            'Texte simple'         => 'text',
            'Email'                => 'email',
            'Numéro'               => 'number',
            'Texte long'           => 'textarea',
            'Date'                 => 'date',
            'Select (choix)'       => 'select',
            'Radio'                => 'radio',
            'Checkbox'             => 'checkbox',
            'Téléphone'            => 'tel',
            'Fichier'              => 'file',
        ];

        //yield FormField::addFieldset('Informations');

        yield TextField::new('label', 'Label du champ')->setColumns(12);

        yield TextField::new('name')->onlyOnForms()->hideOnForm();

        // yield SlugField::new('name', 'Clé technique')
        //     ->setTargetFieldName('label')
        //     ->setRequired(false)
        //     ->setHelp('ex: nom, email, message')
        //     ->setColumns(12)
        //     ->hideOnIndex();

        yield ChoiceField::new('type', 'Type de champ')
            ->setChoices($fieldTypes)
            ->renderAsNativeWidget()
            ->setRequired(true)
            ->setColumns(12);

        yield IntegerField::new('position', 'Ordre')->setColumns(12);

        yield TextareaField::new('options', 'Options (JSON)')
            ->hideOnIndex()
            ->setHelp('Ex: ["Option A", "Option B"] — uniquement pour select / radio / checkbox')
            ->setColumns(12);

        yield AssociationField::new('form', 'Formulaire associé')
            ->setRequired(true)
            ->hideOnForm()
            ->hideOnIndex()
            ->setColumns(6);

        yield BooleanField::new('required', 'Obligatoire')->setColumns(12);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Champs')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer un champ')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier un champ')
            ->setDefaultSort(['position' => 'ASC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions;
    }

    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if ($entityInstance instanceof Field) {
            if (!$entityInstance->getName() && $entityInstance->getLabel()) {
                $slug = strtolower($this->slugger->slug($entityInstance->getLabel()));
                $entityInstance->setName($slug);
            }
        }

        parent::persistEntity($em, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if ($entityInstance instanceof Field) {
            // Si name est vide → regénération
            if (!$entityInstance->getName() && $entityInstance->getLabel()) {
                $slug = strtolower($this->slugger->slug($entityInstance->getLabel()));
                $entityInstance->setName($slug);
            }
        }

        parent::updateEntity($em, $entityInstance);
    }
}
