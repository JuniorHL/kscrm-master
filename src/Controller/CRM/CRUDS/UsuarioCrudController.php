<?php

namespace App\Controller\CRM\CRUDS;

use App\Entity\Cliente;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Func;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsuarioCrudController extends AbstractCrudController
{
	public function __construct(private EntityManagerInterface $entityManagerInterface, private UserPasswordHasherInterface $userPasswordHasherInterface){}

    public static function getEntityFqcn(): string
    {
        return Usuario::class;
    }

	public function configureCrud(Crud $crud): Crud
	{
    	return $crud
        	->setEntityLabelInSingular('Usuario')
        	->setEntityLabelInPlural('Usuarios')
    	;
	}

    public function configureFields(string $pageName): iterable
	{
		$roles = ['Gestor' => "ROLE_GESTOR", 'Invitado' => "ROLE_INVITADO", 'Usuario' => "ROLE_USUARIO"];

        return[ 
            TextField::new('usu_correo', 'Correo'),
			TextField::new('plainPassword', 'Contraseña')
				->onlyOnForms()
				->setFormType(PasswordType::class)
				->setRequired($pageName === Crud::PAGE_NEW)
				->setHelp('Actualizar: Dejar en blanco mantiene la contraseña actual.')
			,
			ChoiceField::new('roles', 'Roles')
				->setChoices($roles)
				->allowMultipleChoices(true)
				->renderAsBadges()
			,
			BooleanField::new('isVerified', '¿Correo verificado?')
				->hideOnForm()
				->renderAsSwitch(false)
			,
			BooleanField::new('usu_estado', '¿Activo?')->hideOnIndex()->hideOnForm()->hideOnDetail(),
        ];
	}

	public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
	{
		$qb = $this->entityManagerInterface->createQueryBuilder();

		$qb->select('usuario')
	 		->from($entityDto->getFqcn(), 'usuario')
			->where('usuario.usu_estado = :usu_estado')
			->setParameter('usu_estado', true)
		;

		return $qb;
	}

	public function createEntity(string $entityFqcn)
	{
		$entity = new $entityFqcn();
		if($entity instanceof Usuario){
			$entity->setUsuEstado(1);
		}

		return $entity;
	}

	public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
	{
		if (!$entityInstance instanceof Usuario) return;

		$encodePassword = $this->userPasswordHasherInterface->hashPassword($entityInstance, $entityInstance->getPlainPassword());
		$entityInstance->setPassword($encodePassword);
		//Verificar el correo
		parent::persistEntity($entityManager, $entityInstance);
	}

	public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
	{
		if (!$entityInstance instanceof Usuario) return;
		
		$plainPassword = $entityInstance->getPlainPassword();

		if(!empty($plainPassword)){
			$encodePassword = $this->userPasswordHasherInterface->hashPassword($entityInstance, $entityInstance->getPlainPassword());
			$entityInstance->setPassword($encodePassword);
		}

		parent::updateEntity($entityManager, $entityInstance);
	}

	public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
	{
		$entityInstance->setUsuEstado(0);

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
