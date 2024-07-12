<?php

namespace App\Entity;

use App\Repository\ProyectoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProyectoRepository::class)]
class Proyecto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $pyt_nombre = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $pyt_primercontacto = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $pyt_descripcion = null;

    #[ORM\Column]
    private ?bool $pyt_estado = null;

    #[ORM\ManyToOne(inversedBy: 'cli_proyectos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cliente $pyt_cliente = null;

    /**
     * @var Collection<int, version>
     */
    #[ORM\OneToMany(targetEntity: Version::class, mappedBy: 'vs_proyecto', cascade: ["persist"])]
    private Collection $pyt_versiones;

    public function __construct()
    {
        $this->pyt_versiones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPytNombre(): ?string
    {
        return $this->pyt_nombre;
    }

    public function setPytNombre(string $pyt_nombre): static
    {
        $this->pyt_nombre = $pyt_nombre;

        return $this;
    }

    public function getPytPrimercontacto(): ?\DateTimeInterface
    {
        return $this->pyt_primercontacto;
    }

    public function setPytPrimercontacto(\DateTimeInterface $pyt_primercontacto): static
    {
        $this->pyt_primercontacto = $pyt_primercontacto;

        return $this;
    }

    public function getPytDescripcion(): ?string
    {
        return $this->pyt_descripcion;
    }

    public function setPytDescripcion(string $pyt_descripcion): static
    {
        $this->pyt_descripcion = $pyt_descripcion;

        return $this;
    }

    public function isPytEstado(): ?bool
    {
        return $this->pyt_estado;
    }

    public function setPytEstado(bool $pyt_estado): static
    {
        $this->pyt_estado = $pyt_estado;

        return $this;
    }

    public function getPytCliente(): ?Cliente
    {
        return $this->pyt_cliente;
    }

    public function setPytCliente(?Cliente $pyt_cliente): static
    {
        $this->pyt_cliente = $pyt_cliente;

        return $this;
    }

    /**
     * @return Collection<int, version>
     */
    public function getPytVersiones(): Collection
    {
        return $this->pyt_versiones;
    }

    public function addPytVersione(version $pytVersione): static
    {
        if (!$this->pyt_versiones->contains($pytVersione)) {
            $this->pyt_versiones->add($pytVersione);
            $pytVersione->setVsProyecto($this);
        }

        return $this;
    }

    public function removePytVersione(version $pytVersione): static
    {
        if ($this->pyt_versiones->removeElement($pytVersione)) {
            // set the owning side to null (unless already changed)
            if ($pytVersione->getVsProyecto() === $this) {
                $pytVersione->setVsProyecto(null);
            }
        }

        return $this;
    }
}
