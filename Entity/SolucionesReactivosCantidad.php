<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SolucionesReactivosCantidadRepository")
 */
class SolucionesReactivosCantidad
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Soluciones", inversedBy="solucionesReactivosCantidad")
     * @ORM\JoinColumn(nullable=false)
     */
    private $soluciones;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Reactivos", inversedBy="solucionesReactivosCantidad")
     * @ORM\JoinColumn(nullable=false)
     */
    private $reactivos;

    /**
     * @ORM\Column(type="integer")
     */
    private $cantidad_reactivo;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSoluciones(): ?Soluciones
    {
        return $this->soluciones;
    }

    public function setSoluciones(?Soluciones $soluciones): self
    {
        $this->soluciones = $soluciones;

        return $this;
    }

    public function getReactivos(): ?Reactivos
    {
        return $this->reactivos;
    }

    public function setReactivos(?Reactivos $reactivos): self
    {
        $this->reactivos = $reactivos;

        return $this;
    }

    public function getCantidadReactivo(): ?int
    {
        return $this->cantidad_reactivo;
    }

    public function setCantidadReactivo(int $cantidad_reactivo): self
    {
        $this->cantidad_reactivo = $cantidad_reactivo;

        return $this;
    }
}
