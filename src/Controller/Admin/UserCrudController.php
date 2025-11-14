<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $password = TextField::new('clearpassword')
                                ->setLabel("Nouveau mot de passe")
                                //->setFormType(PasswordType::class)
                                ->setFormTypeOption('empty_data', '')
                                ->setRequired(false)
                                ->setHelp('Si pas de changement, laissez le champ vide')
                                ->hideOnIndex()
                                ->hideOnDetail()->setColumns(12);


        return [
            IdField::new('id')->onlyOnIndex(),

            FormField::addPanel('Informations')->setCssClass('col-sm-6 p-4'),
            TextField::new('userName', 'Nom / Prénom')->setColumns(6),
            EmailField::new('email', 'Adresse e-mail')->setColumns(6),

            FormField::addPanel('Droits & Roles')->setCssClass('col-sm-6 p-4'),

            ChoiceField::new('role')
                ->setChoices([
                    'Membre' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ])
                ->renderExpanded(false) // optionnel : menu déroulant (default)
                ->setRequired(true)
                ->setColumns(12),

            FormField::addPanel('Sécurité')->setCssClass('col-sm-6 p-4'),
            $password,
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
            ->setPageTitle('index', 'Liste des utilisateurs')
            ->setPageTitle('new', 'Nouveau utilisateur')
            ->setPageTitle('detail', 'Détail utilisateur')
            ->setPageTitle('edit', 'Modifier un utilisateur');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::DELETE);
    }

    public function createEntity(string $entityFqcn)
    {
        $dateNow = new \DateTimeImmutable('now');

        $newUser = new User();

        $newUser
                ->setCreatedAt($dateNow)
                ->setUpdatedAt($dateNow);

        return $newUser;
    }
}
