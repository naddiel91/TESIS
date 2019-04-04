<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReactivosRepository")
 */
class Reactivos
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
     * @ORM\Column(type="string", length=100)
     */
    private $nombre_quimico;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $formula;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $codigo_comercial;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $envase_comercial;

    /**
     * @ORM\Column(type="integer")
     */
    private $cantidad;

    /**
     * @ORM\Column(type="integer")
     */
    private $cantidad_minima;

    /**
     * @var string
     * @ORM\ManyToMany(targetEntity="Categoria")
     * @ORM\JoinTable(name="reactivos_categoria",
     *      joinColumns={@ORM\JoinColumn(name="reactivos_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="categoria_id", referencedColumnName="id")}
     *      )
     */
    private $categoria;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Soluciones", mappedBy="reactivos")
     */
    private $soluciones;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $precio;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $proveedor;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $estado_fisico;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $sinonimo;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $unidad;


    public function __construct()
    {
        $this->categoria = new ArrayCollection();
        $this->soluciones = new ArrayCollection();
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

    public function getNombreQuimico(): ?string
    {
        return $this->nombre_quimico;
    }

    public function setNombreQuimico(string $nombre_quimico): self
    {
        $this->nombre_quimico = $nombre_quimico;

        return $this;
    }

    public function getFormula(): ?string
    {
        return $this->formula;
    }

    public function setFormula(string $formula): self
    {
        $this->formula = $formula;

        return $this;
    }

    public function getCodigoComercial(): ?string
    {
        return $this->codigo_comercial;
    }

    public function setCodigoComercial(string $codigo_comercial): self
    {
        $this->codigo_comercial = $codigo_comercial;

        return $this;
    }

    public function getEnvaseComercial(): ?string
    {
        return $this->envase_comercial;
    }

    public function setEnvaseComercial(string $envase_comercial): self
    {
        $this->envase_comercial = $envase_comercial;

        return $this;
    }

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): self
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getCantidadMinima(): ?int
    {
        return $this->cantidad_minima;
    }

    public function setCantidadMinima(int $cantidad_minima): self
    {
        $this->cantidad_minima = $cantidad_minima;

        return $this;
    }

    /**
     * Get categoria
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategoria(): Collection
    {
        return $this->categoria;
    }

    /**
     * Set categoria
     *
     * @param Categoria $categoria
     * @return Reactivos
     */
    public function setCategoria(Categoria $categoria): self
    {
        $this->categoria = $categoria;

        return $this;
    }

    public function addCategoria(Categoria $categoria)
    {
        $this->categoria[] = $categoria;
    }

    public function removeCategoria($categoria)
    {
        $this->categoria->remove($categoria);
    }

    public function __toString()
    {
        return $this->nombre != "" ? $this->nombre : "";
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
            $solucione->addReactivo($this);
        }

        return $this;
    }

    public function removeSolucione(Soluciones $solucione): self
    {
        if ($this->soluciones->contains($solucione)) {
            $this->soluciones->removeElement($solucione);
            $solucione->removeReactivo($this);
        }

        return $this;
    }

    public function getPrecio()
    {
        return $this->precio;
    }

    public function setPrecio($precio): self
    {
        $this->precio = $precio;

        return $this;
    }

    public function getProveedor(): ?string
    {
        return $this->proveedor;
    }

    public function setProveedor(?string $proveedor): self
    {
        $this->proveedor = $proveedor;

        return $this;
    }

    public function getEstadoFisico(): ?string
    {
        return $this->estado_fisico;
    }

    public function setEstadoFisico(string $estado_fisico): self
    {
        $this->estado_fisico = $estado_fisico;

        return $this;
    }

    public function getSinonimo(): ?string
    {
        return $this->sinonimo;
    }

    public function setSinonimo(?string $sinonimo): self
    {
        $this->sinonimo = $sinonimo;

        return $this;
    }

    public function getUnidad(): ?string
    {
        return $this->unidad;
    }

    public function setUnidad(string $unidad): self
    {
        $this->unidad = $unidad;

        return $this;
    }
}
