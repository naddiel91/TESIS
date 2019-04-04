<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnalisisRepository")
 */
class Analisis
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
     * @ORM\OneToMany(targetEntity="App\Entity\Soluciones", mappedBy="analisis")
     */
    private $soluciones;

    public function __construct()
    {
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
            $solucione->setAnalisis($this);
        }

        return $this;
    }

    public function removeSolucione(Soluciones $solucione): self
    {
        if ($this->soluciones->contains($solucione)) {
            $this->soluciones->removeElement($solucione);
            // set the owning side to null (unless already changed)
            if ($solucione->getAnalisis() === $this) {
                $solucione->setAnalisis(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nombre;
    }
}
