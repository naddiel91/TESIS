<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SolucionesRepository")
 */
class Soluciones
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descripcion;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Analisis", inversedBy="soluciones")
     * @ORM\JoinColumn(nullable=false)
     */
    private $analisis;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Reactivos", inversedBy="soluciones")
     */
    private $reactivos;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\EnsayosRealizados", mappedBy="soluciones")
     */
    private $ensayo_realizado;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $analista;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $fecha_creada;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $fecha_vencimiento;

    public function __construct()
    {
        $this->reactivos = new ArrayCollection();
        $this->ensayo_realizado = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getAnalisis(): ?Analisis
    {
        return $this->analisis;
    }

    public function setAnalisis(?Analisis $analisis): self
    {
        $this->analisis = $analisis;

        return $this;
    }

    /**
     * @return Collection|Reactivos[]
     */
    public function getReactivos(): Collection
    {
        return $this->reactivos;
    }

    public function addReactivo($reactivo): self
    {
        if (!$this->reactivos->contains($reactivo)) {
            $this->reactivos[] = $reactivo;
        }

        return $this;
    }

    public function removeReactivo(Reactivos $reactivo): self
    {
        if ($this->reactivos->contains($reactivo)) {
            $this->reactivos->removeElement($reactivo);
        }

        return $this;
    }

    public function removeAllReactivo(): self
    {
        $this->reactivos = new ArrayCollection();
        return $this;
    }

    /**
     * @return Collection|EnsayosRealizados[]
     */
    public function getEnsayoRealizado(): Collection
    {
        return $this->ensayo_realizado;
    }

    public function addEnsayoRealizado(EnsayosRealizados $ensayoRealizado): self
    {
        if (!$this->ensayo_realizado->contains($ensayoRealizado)) {
            $this->ensayo_realizado[] = $ensayoRealizado;
            $ensayoRealizado->addSolucione($this);
        }

        return $this;
    }

    public function removeEnsayoRealizado(EnsayosRealizados $ensayoRealizado): self
    {
        if ($this->ensayo_realizado->contains($ensayoRealizado)) {
            $this->ensayo_realizado->removeElement($ensayoRealizado);
            $ensayoRealizado->removeSolucione($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nombre;
    }

    public function getAnalista(): ?string
    {
        return $this->analista;
    }

    public function setAnalista(string $analista): self
    {
        $this->analista = $analista;

        return $this;
    }

    public function getFechaCreada(): ?string
    {
        return $this->fecha_creada;
    }

    public function setFechaCreada(string $fecha_creada): self
    {
        $this->fecha_creada = $fecha_creada;

        return $this;
    }

    public function getFechaVencimiento(): ?string
    {
        return $this->fecha_vencimiento;
    }

    public function setFechaVencimiento(string $fecha_vencimiento): self
    {
        $this->fecha_vencimiento = $fecha_vencimiento;

        return $this;
    }
}
