<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PhpParser\Node\Scalar\String_;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EnsayosRealizadosRepository")
 */
class EnsayosRealizados
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nombre_analisis;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Metodo")
     * @ORM\JoinColumn(nullable=false)
     */
    private $metodo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comentario;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $fecha;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $hecho_por;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Soluciones", inversedBy="ensayo_realizado")
     */
    private $soluciones;

    /**
     * @ORM\Column(type="integer")
     */
    private $ensayo_en_unidad;

    /**
     * @ORM\Column(type="integer")
     */
    private $punto;

    public function __construct()
    {
        $this->soluciones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreAnalisis(): ?string
    {
        return $this->nombre_analisis;
    }

    public function setNombreAnalisis(?string $nombre_analisis): self
    {
        $this->nombre_analisis = $nombre_analisis;

        return $this;
    }

    public function getMetodo(): ?Metodo
    {
        return $this->metodo;
    }

    public function setMetodo(?Metodo $metodo): self
    {
        $this->metodo = $metodo;

        return $this;
    }

    public function getComentario(): ?string
    {
        return $this->comentario;
    }

    public function setComentario(?string $comentario): self
    {
        $this->comentario = $comentario;

        return $this;
    }

    public function getFecha(): ?string
    {
        return $this->fecha;
    }

    public function setFecha(String $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getHechoPor(): ?string
    {
        return $this->hecho_por;
    }

    public function setHechoPor(string $hecho_por): self
    {
        $this->hecho_por = $hecho_por;

        return $this;
    }

    /**
     * @return Collection|Soluciones[]
     */
    public function getSoluciones(): Collection
    {
        return $this->soluciones;
    }

    public function addSolucione(Soluciones $solucione): self
    {
        if (!$this->soluciones->contains($solucione)) {
            $this->soluciones[] = $solucione;
        }

        return $this;
    }

    public function removeSolucione(Soluciones $solucione): self
    {
        if ($this->soluciones->contains($solucione)) {
            $this->soluciones->removeElement($solucione);
        }

        return $this;
    }

    public function removeAllSoluciones()
    {
        $this->soluciones = new ArrayCollection();

        return $this;
    }

    public function getEnsayoEnUnidad(): ?int
    {
        return $this->ensayo_en_unidad;
    }

    public function setEnsayoEnUnidad(int $ensayo_en_unidad): self
    {
        $this->ensayo_en_unidad = $ensayo_en_unidad;

        return $this;
    }

    public function getPunto(): ?int
    {
        return $this->punto;
    }

    public function setPunto(int $punto): self
    {
        $this->punto = $punto;

        return $this;
    }
}
