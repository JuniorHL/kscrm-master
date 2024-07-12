<?php

namespace App\Controller\CRM\CRUDS;

use App\Entity\Cliente;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ClienteCrudController extends AbstractCrudController
{
	public function __construct(private EntityManagerInterface $entityManagerInterface){}

    public static function getEntityFqcn(): string
    {
        return Cliente::class;
    }

	public function configureCrud(Crud $crud): Crud
	{
    	return $crud
        	->setEntityLabelInSingular('Cliente')
        	->setEntityLabelInPlural('Clientes')
    	;
	}

	public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('cli_nombres', 'Nombres'),
            TextField::new('cli_apepat', 'Apellido Paterno'),
			TextField::new('cli_apemat', 'Apellido Materno'),
			TextField::new('cli_dni', 'DNI'),
			TextField::new('cli_correo', 'Correo'),
			TextField::new('cli_telefono', 'Telefono'),
			TextareaField::new('cli_direccion', 'Dirección'),
			BooleanField::new('cli_estado', 'Estado')->hideOnIndex()->hideOnForm()->hideOnDetail(),
		];
	}

	public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
	{
   		$qb = $this->entityManagerInterface->createQueryBuilder();

		$qb->select('cliente')
			->from($entityDto->getFqcn(), 'cliente')
			->where('cliente.cli_estado = :cli_estado')
			->setParameter('cli_estado', true);

		return $qb;	
	}
	
	public function createEntity(string $entityFqcn)
	{
		$entity = new $entityFqcn();
		if($entity instanceof Cliente){
			$entity->setCliEstado(1);
		}

		return $entity;
	}

	public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
	{
		$entityInstance->setCliEstado(0);

		$entityManager->persist($entityInstance);
		$entityManager->flush();
	}	

	public function configureActions(Actions $actions): Actions
	{
		return $actions
			->add(Crud::PAGE_INDEX, Action::DETAIL)
			->add(Crud::PAGE_EDIT, Action::DELETE)
			->setPermission(Action::NEW, 'ROLE_GESTOR')
			->setPermission(Action::DELETE, 'ROLE_GESTOR')
			->setPermission(Action::EDIT, 'ROLE_GESTOR')
		;
	}
}
