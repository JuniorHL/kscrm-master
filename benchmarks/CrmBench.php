<?php

namespace App\Benchmarks;

use App\Entity\Cliente;
use App\Entity\Proyecto;
use App\Entity\Usuario;
use App\Entity\Version;
use App\Repository\ClienteRepository;
use App\Repository\ProyectoRepository;
use App\Repository\UsuarioRepository;
use App\Repository\VersionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Dotenv\Dotenv;

/**
 * @BeforeMethods({"initBenchmark"})
 */
class CrmBench
{
    private ?EntityManagerInterface $entityManager = null;
    private ?ClienteRepository $clienteRepository = null;
    private ?ProyectoRepository $proyectoRepository = null;
    private ?UsuarioRepository $usuarioRepository = null;
    private ?VersionRepository $versionRepository = null;

    public function initBenchmark(): void
    {
        if ($this->entityManager === null) {
            $dotenv = new Dotenv();
            $dotenv->bootEnv(dirname(__DIR__).'/.env');
            $kernel = new \App\Kernel('test', true);
            $kernel->boot();
            $container = $kernel->getContainer();
            $this->entityManager = $container->get('doctrine.orm.entity_manager');
            $this->clienteRepository = $this->entityManager->getRepository(Cliente::class);
            $this->proyectoRepository = $this->entityManager->getRepository(Proyecto::class);
            $this->usuarioRepository = $this->entityManager->getRepository(Usuario::class);
            $this->versionRepository = $this->entityManager->getRepository(Version::class);
        }
    }

    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchListarClientes()
    {
        $this->clienteRepository->findAll();
    }

    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchBuscarClientePorNombre()
    {
        $this->clienteRepository->findOneBy(['cli_nombres' => 'Cliente Ejemplo']);
    }

    /**
     * @Revs(50)
     * @Iterations(5)
     */
    public function benchCrearNuevoCliente()
    {
        $cliente = new Cliente();
        $cliente->setCliNombres('Nuevo Cliente');
        $cliente->setCliApepat('Apellido Paterno');
        $cliente->setCliApemat('Apellido Materno');
        $cliente->setCliDni('12345678');
        $cliente->setCliCorreo('nuevo@ejemplo.com');
        $cliente->setCliTelefono('987654321');
        $cliente->setCliDireccion('Direccion Ejemplo');
        $cliente->setCliEstado(true);
        $this->entityManager->persist($cliente);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchListarProyectosPorCliente()
    {
        $cliente = $this->clienteRepository->findOneBy([]);
        if ($cliente) {
            $this->proyectoRepository->findBy(['pyt_cliente' => $cliente]);
        }
    }

    /**
     * @Revs(50)
     * @Iterations(5)
     */
    public function benchCrearNuevoProyecto()
    {
        $cliente = $this->clienteRepository->findOneBy([]);
        $usuario = $this->usuarioRepository->findOneBy([]);
        if ($cliente && $usuario) {
            $proyecto = new Proyecto();
            $proyecto->setPytNombre('Nuevo Proyecto');
            $proyecto->setPytPrimercontacto(new \DateTime());
            $proyecto->setPytDescripcion('Descripcion del proyecto');
            $proyecto->setPytEstado(true);
            $proyecto->setPytCliente($cliente);
            // Asignar el usuario responsable si hay una relación
            $this->entityManager->persist($proyecto);
            $this->entityManager->flush();
            $this->entityManager->clear();
        }
    }

    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchListarVersionesPorProyecto()
    {
        $proyecto = $this->proyectoRepository->findOneBy([]);
        if ($proyecto) {
            $this->versionRepository->findBy(['vs_proyecto' => $proyecto]);
        }
    }

    /**
     * @Revs(50)
     * @Iterations(5)
     */
    public function benchActualizarUsuario()
    {
        $usuario = $this->usuarioRepository->findOneBy([]);
        if ($usuario) {
            $usuario->setUsuCorreo($usuario->getUsuCorreo() . '.updated@example.com');
            $this->entityManager->flush();
            $this->entityManager->clear();
        }
    }

    /**
     * @Revs(50)
     * @Iterations(5)
     */
    public function benchDashboardStats()
    {
        $clientesCount = $this->clienteRepository->count([]);
        $proyectosCount = $this->proyectoRepository->count([]);
        $usuariosCount = $this->usuarioRepository->count([]);
        $versionesRecientes = $this->versionRepository->createQueryBuilder('v')
            ->orderBy('v.vs_fechainicio', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
}
