<?php

namespace App\Entity;

use App\Repository\VersionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VersionRepository::class)]
class Version
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $vs_descripcion = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $vs_fechainicio = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $vs_fechafinestimada = null;

    #[ORM\Column]
    private ?float $vs_duracion = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $vs_planificacion = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $vs_presupuesto = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $vs_alcance = null;

    #[ORM\Column]
    private ?bool $vs_estado = true;

    #[ORM\ManyToOne(inversedBy: 'pyt_versiones')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Proyecto $vs_proyecto = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVsDescripcion(): ?string
    {
        return $this->vs_descripcion;
    }

    public function setVsDescripcion(string $vs_descripcion): static
    {
        $this->vs_descripcion = $vs_descripcion;

        return $this;
    }

    public function getVsFechainicio(): ?\DateTimeInterface
    {
        return $this->vs_fechainicio;
    }

    public function setVsFechainicio(\DateTimeInterface $vs_fechainicio): static
    {
        $this->vs_fechainicio = $vs_fechainicio;

        return $this;
    }

    public function getVsFechafinestimada(): ?\DateTimeInterface
    {
        return $this->vs_fechafinestimada;
    }

    public function setVsFechafinestimada(\DateTimeInterface $vs_fechafinestimada): static
    {
        $this->vs_fechafinestimada = $vs_fechafinestimada;

        return $this;
    }

    public function getVsDuracion(): ?float
    {
        return $this->vs_duracion;
    }

    public function setVsDuracion(float $vs_duracion): static
    {
        $this->vs_duracion = $vs_duracion;

        return $this;
    }

    public function getVsPlanificacion(): ?string
    {
        return $this->vs_planificacion;
    }

    public function setVsPlanificacion(?string $vs_planificacion): static
    {
        $this->vs_planificacion = $vs_planificacion;

        return $this;
    }

    public function getVsPresupuesto(): ?string
    {
        return $this->vs_presupuesto;
    }

    public function setVsPresupuesto(string $vs_presupuesto): static
    {
        $this->vs_presupuesto = $vs_presupuesto;

        return $this;
    }

    public function getVsAlcance(): ?string
    {
        return $this->vs_alcance;
    }

    public function setVsAlcance(string $vs_alcance): static
    {
        $this->vs_alcance = $vs_alcance;

        return $this;
    }

    public function isVsEstado(): ?bool
    {
        return $this->vs_estado;
    }

    public function setVsEstado(bool $vs_estado): static
    {
        $this->vs_estado = $vs_estado;

        return $this;
    }

    public function getVsProyecto(): ?Proyecto
    {
        return $this->vs_proyecto;
    }

    public function setVsProyecto(?Proyecto $vs_proyecto): static
    {
        $this->vs_proyecto = $vs_proyecto;

        return $this;
    }

    public function __toString()
    {
        return $this->vs_descripcion;
    }
}
