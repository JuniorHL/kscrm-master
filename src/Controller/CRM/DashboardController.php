<?php

namespace App\Controller\CRM;

use App\Controller\CRM\CRUDS\ClienteCrudController;
use App\Entity\Cliente;
use App\Entity\Proyecto;
use App\Entity\Usuario;
use App\Repository\ClienteRepository;
use App\Repository\ProyectoRepository;
use App\Repository\UsuarioRepository;
use App\Repository\VersionRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    private $usuarioRepository;
    private $clienteRepository;
    private $proyectoRepository;
    private $versionRepository;

    public function __construct(
        UsuarioRepository $usuarioRepository,
        ClienteRepository $clienteRepository,
        ProyectoRepository $proyectoRepository,
        VersionRepository $versionRepository
    ) {
        $this->usuarioRepository = $usuarioRepository;
        $this->clienteRepository = $clienteRepository;
        $this->proyectoRepository = $proyectoRepository;
        $this->versionRepository = $versionRepository;
    }

    #[Route('/crm', name: 'app_crm_dashboard_mostrar')]
    public function index(): Response
    {
        $totalUsuarios = $this->usuarioRepository->count([]);
        $totalClientes = $this->clienteRepository->count([]);
        $totalProyectos = $this->proyectoRepository->count([]);
        $totalVersiones = $this->versionRepository->count([]);
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(ClienteCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('crm/dashboard/index.html.twig',[
            'totalUsuarios' => $totalUsuarios,
            'totalClientes' => $totalClientes,
            'totalProyectos' => $totalProyectos,
            'totalVersiones' => $totalVersiones,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
			->setTitle('KSCRM')
            ->renderContentMaximized()
			->disableDarkMode()
		;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
		yield MenuItem::section('Trabajos');
		yield MenuItem::linkToCrud('Proyectos', 'fa fa-briefcase', Proyecto::class);
		yield MenuItem::linkToCrud('Clientes', 'fa fa-user', Cliente::class);

		yield MenuItem::section('Seguridad');
 		yield MenuItem::linkToCrud('Usuarios', 'fa fa-users', Usuario::class);

		yield MenuItem::section('Sitios');
        yield MenuItem::linkToUrl('KSPERU', 'fas fa-leaf', 'https://www.ksperu.com') ->setLinkTarget('_blanc');
        yield MenuItem::linkToUrl('Correo', 'fas fa-envelope', 'https://correo.ksperu.com') ->setLinkTarget('_blanc');
        yield MenuItem::linkToUrl('Redmine', 'fas fa-folder', 'https://redmine.ksperu.com') ->setLinkTarget('_blanc');
        yield MenuItem::linkToUrl('Dokuwiki', 'fas fa-file-text', 'https://dokuwiki.ksperu.com') ->setLinkTarget('_blanc');
        yield MenuItem::linkToUrl('Jitsi Meet', 'fas fa-video-camera', 'https://jitsi.ksperu.com') ->setLinkTarget('_blanc');
        yield MenuItem::linkToUrl('NPM', 'fas fa-globe', 'https://npm.ksperu.com') ->setLinkTarget('_blanc');
        yield MenuItem::linkToUrl('Pfsense', 'fas fa-fire', 'https://pfsense.ksperu.com') ->setLinkTarget('_blanc');
        yield MenuItem::linkToUrl('Tiempo', 'fas fa-clock', 'https://tiempo.ksperu.com') ->setLinkTarget('_blanc');
        yield MenuItem::linkToUrl('PhpMyAdmin', 'fas fa-database', 'https://phpmyadmin.ksperu.com') ->setLinkTarget('_blanc');
        yield MenuItem::linkToUrl('Proxmox', 'fas fa-server', 'https://proxmox.ksperu.com') ->setLinkTarget('_blanc');
	}
}
