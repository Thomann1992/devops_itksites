<?php

namespace App\Controller\Admin;

use App\Entity\Server;
use App\Form\Type\Admin\HostingProviderFilter;
use App\Form\Type\Admin\MariaDbVersionFilter;
use App\Form\Type\Admin\SystemFilter;
use App\Types\DatabaseVersionType;
use App\Types\HostingProviderType;
use App\Types\SystemType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ServerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Server::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('Provider Details');
        yield TextField::new('name')->setColumns(8);
        yield TextField::new('hostingProviderName')->setColumns(4)->hideOnIndex();
        yield TextField::new('apiKey')->setColumns(8)->setFormTypeOptions(['disabled' => 'true'])->onlyOnDetail();
        yield ChoiceField::new('hostingProvider')->setChoices(HostingProviderType::CHOICES)->setColumns(4);
        yield FormField::addPanel('Network');
        yield TextField::new('internalIp')->setColumns(6);
        yield TextField::new('externalIp')->setColumns(6);
        yield FormField::addPanel('Options');
        yield BooleanField::new('aarhusSsl')->setColumns(3)->hideOnIndex();
        yield BooleanField::new('letsEncryptSsl')->setColumns(3)->hideOnIndex();
        yield BooleanField::new('veeam')->setColumns(2)->hideOnIndex();
        yield BooleanField::new('azureBackup')->setColumns(2)->hideOnIndex();
        yield BooleanField::new('monitoring')->setColumns(2)->hideOnIndex();
        yield FormField::addPanel('System Details');
        yield ChoiceField::new('databaseVersion')->setChoices(DatabaseVersionType::CHOICES)->renderExpanded()->setColumns(6);
        yield ChoiceField::new('system')->setChoices(SystemType::CHOICES)->renderExpanded()->setColumns(6);
        yield FormField::addPanel('Miscellaneous');
        yield TextField::new('serviceDeskTicket')->setColumns(12)->hideOnIndex();
        yield TextareaField::new('note')->hideOnIndex()->setColumns(6);
        yield TextareaField::new('usedFor')->hideOnIndex()->setColumns(6);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add(HostingProviderFilter::new('hostingProvider'))
            ->add(MariaDbVersionFilter::new('databaseVersion'))
            ->add(SystemFilter::new('system'))
            ;
    }
}
