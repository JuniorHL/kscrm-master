<?php

namespace App\Entity;

use App\Repository\ClienteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClienteRepository::class)]
class Cliente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    private ?string $cli_nombres = null;

    #[ORM\Column(length: 64)]
    private ?string $cli_apepat = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $cli_apemat = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $cli_dni = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cli_correo = null;

    #[ORM\Column(length: 9)]
    private ?string $cli_telefono = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $cli_direccion = null;

    #[ORM\Column]
    private ?bool $cli_estado = null;

    /**
     * @var Collection<int, proyecto>
     */
    #[ORM\OneToMany(targetEntity: Proyecto::class, mappedBy: 'pyt_cliente')]
    private Collection $cli_proyectos;

    public function __construct()
    {
        $this->cli_proyectos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCliNombres(): ?string
    {
        return $this->cli_nombres;
    }

    public function setCliNombres(string $cli_nombres): static
    {
        $this->cli_nombres = $cli_nombres;

        return $this;
    }

    public function getCliApepat(): ?string
    {
        return $this->cli_apepat;
    }

    public function setCliApepat(string $cli_apepat): static
    {
        $this->cli_apepat = $cli_apepat;

        return $this;
    }

    public function getCliApemat(): ?string
    {
        return $this->cli_apemat;
    }

    public function setCliApemat(?string $cli_apemat): static
    {
        $this->cli_apemat = $cli_apemat;

        return $this;
    }

    public function getCliDni(): ?string
    {
        return $this->cli_dni;
    }

    public function setCliDni(?string $cli_dni): static
    {
        $this->cli_dni = $cli_dni;

        return $this;
    }

    public function getCliCorreo(): ?string
    {
        return $this->cli_correo;
    }

    public function setCliCorreo(?string $cli_correo): static
    {
        $this->cli_correo = $cli_correo;

        return $this;
    }

    public function getCliTelefono(): ?string
    {
        return $this->cli_telefono;
    }

    public function setCliTelefono(string $cli_telefono): static
    {
        $this->cli_telefono = $cli_telefono;

        return $this;
    }

    public function getCliDireccion(): ?string
    {
        return $this->cli_direccion;
    }

    public function setCliDireccion(string $cli_direccion): static
    {
        $this->cli_direccion = $cli_direccion;

        return $this;
    }

    public function isCliEstado(): ?bool
    {
        return $this->cli_estado;
    }

    public function setCliEstado(bool $cli_estado): static
    {
        $this->cli_estado = $cli_estado;

        return $this;
    }

    /**
     * @return Collection<int, proyecto>
     */
    public function getCliProyectos(): Collection
    {
        return $this->cli_proyectos;
    }

    public function addCliProyecto(proyecto $cliProyecto): static
    {
        if (!$this->cli_proyectos->contains($cliProyecto)) {
            $this->cli_proyectos->add($cliProyecto);
            $cliProyecto->setPytCliente($this);
        }

        return $this;
    }

    public function removeCliProyecto(proyecto $cliProyecto): static
    {
        if ($this->cli_proyectos->removeElement($cliProyecto)) {
            // set the owning side to null (unless already changed)
            if ($cliProyecto->getPytCliente() === $this) {
                $cliProyecto->setPytCliente(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getCliNombres().' '.$this->getCliApepat().' '.$this->getCliApemat();
    } 
}
