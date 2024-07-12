<?php

namespace App\Controller\CRM\CRUDS;

use App\Entity\Version;
use Doctrine\ORM\QueryBuilder;
use PhpParser\Node\Stmt\Return_;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class VersionCrudController extends AbstractCrudController
{
	public function __construct(private EntityManagerInterface $entityManagerInterface){}

    public static function getEntityFqcn(): string
    {
        return Version::class;
    }

    public function configureCrud(Crud $crud): Crud
	{
    	return $crud
        	->setEntityLabelInSingular('Version')
        	->setEntityLabelInPlural('Versions')
    	;
	}

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('vs_descripcion', 'Descripción')->setColumns(12),
            DateField::new('vs_fechainicio', 'Fecha de inicio')->setColumns(12),
            DateField::new('vs_fechafinestimada', 'Fecha fin estimada')->setColumns(12),
            NumberField::new('vs_duracion', 'Duración')->setColumns(12),
            TextareaField::new('vs_planificacion', 'Planificación')->setColumns(12),
            TextareaField::new('vs_presupuesto', 'Presupuesto')->setColumns(12),
            TextareaField::new('vs_alcance', 'Alcance')->setColumns(12),
            BooleanField::new('vs_estado')->hideOnDetail()->hideOnForm()->hideOnIndex()->setColumns(12),
        ];
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = $this->entityManagerInterface->createQueryBuilder();

		$qb->select('version')
			->from($entityDto->getFqcn(), 'version')
			->where('version.vs_estado = :vs_estado')
			->setParameter('vs_estado', true);

		return $qb;
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
		if ($entityInstance instanceof Version) {
            $proyecto = $entityInstance->getVsProyecto();
            $proyecto->removePytVersione($entityInstance);
            $entityInstance->setVsEstado(false);
            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }

    public function createEntity(string $entityFqcn)
	{
		$entity = new $entityFqcn();
		if($entity instanceof Version){
			$entity->setVsEstado(true);
		}

		return $entity;
	}
}
