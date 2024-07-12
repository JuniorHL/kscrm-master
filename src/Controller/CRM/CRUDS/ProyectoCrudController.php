<?php

namespace App\Controller\CRM\CRUDS;

use App\Entity\Proyecto;
use Doctrine\ORM\QueryBuilder;
use App\Form\SelectorClienteType;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;
use Symfony\Component\Routing\RouterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use App\Controller\CRM\CRUDS\VersionCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProyectoCrudController extends AbstractCrudController
{
    public function __construct(private RouterInterface $router, private EntityManagerInterface $entityManagerInterface){}

    public static function getEntityFqcn(): string
    {
        return Proyecto::class;
    }

    public function configureCrud(Crud $crud): Crud
	{
    	return $crud
        	->setEntityLabelInSingular('Proyecto')
        	->setEntityLabelInPlural('Proyectos')
    	;
	}

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addFieldset('Cliente'),
            AssociationField::new('pyt_cliente', 'Seleccionar cliente: '),
            FormField::addFieldset('Proyecto'),
            TextField::new('pyt_nombre', 'Nombre de proyecto'),
            DateField::new('pyt_primercontacto', 'Primer contacto'),
            TextareaField::new('pyt_descripcion', 'Descripción'),
            BooleanField::new('pyt_estado', 'Estado')->hideOnIndex()->hideOnForm()->hideOnDetail(),
            FormField::addFieldset('Versiones'),
            CollectionField::new('pyt_versiones', 'Versiones')
                ->useEntryCrudForm(VersionCrudController::class)
                ->hideOnIndex(),
        ];
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = $this->entityManagerInterface->createQueryBuilder();

        $qb->select('proyecto')
            ->from($entityDto->getFqcn(), 'proyecto')
            ->where('proyecto.pyt_estado = :pyt_estado')
            ->setParameter('pyt_estado', true);

        return $qb;
    }

    public function createEntity(string $entityFqcn)
    {
        $entity = new $entityFqcn();
        if ($entity instanceof Proyecto) {
            $entity->setPytEstado(1);
        }

        return $entity;
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityInstance->setPytEstado(0);
        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function configureActions(Actions $actions): Actions
    {
        $url = $this->router->generate('app_crm_dashboard_mostrar');
        $createClientButton = Action::new('Crear cliente')
            ->linkToUrl($url.'?crudAction=new&crudControllerFqcn=App%5CController%5CCRM%5CCRUDS%5CClienteCrudController');

            $generateReportButton = Action::new('Generar Reporte')
            ->linkToRoute('app_reporte_proyectos', function (Proyecto $proyecto): array {
                return ['id' => $proyecto->getId()];
            })
            ->setHtmlAttributes(['target' => '_blank']);

        return $actions
            ->add(Crud::PAGE_EDIT, $createClientButton)
            ->add(Crud::PAGE_NEW, $createClientButton)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DELETE)
            ->add(Crud::PAGE_INDEX, $generateReportButton)
            ->setPermission(Action::NEW, 'ROLE_GESTOR')
			->setPermission(Action::DELETE, 'ROLE_GESTOR')
			->setPermission(Action::EDIT, 'ROLE_GESTOR');
            
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Proyecto) {
            foreach ($entityInstance->getPytVersiones() as $version) {
                $version->setVsProyecto($entityInstance);
            }
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Proyecto) {
            foreach ($entityInstance->getPytVersiones() as $version) {
                $version->setVsProyecto($entityInstance);
            }
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
}
