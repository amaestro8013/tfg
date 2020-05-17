<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Autores
 *
 * @ORM\Table(name="Autores")
 * @ORM\Entity
 */
class Autores
{
    /**
     * @var int
     *
     * @ORM\Column(name="idAutores", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idautores;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=45, nullable=false)
     */
    private $nombre;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Canciones", mappedBy="autoresIdautores")
     */
    private $cancionesIdcanciones;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Etiquetas", mappedBy="autoresIdautores")
     */
    private $etiquetasIdetiquetas;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cancionesIdcanciones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->etiquetasIdetiquetas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getIdautores(): ?int
    {
        return $this->idautores;
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
     * @return Collection|Canciones[]
     */
    public function getCancionesIdcanciones(): Collection
    {
        return $this->cancionesIdcanciones;
    }

    public function addCancionesIdcancione(Canciones $cancionesIdcancione): self
    {
        if (!$this->cancionesIdcanciones->contains($cancionesIdcancione)) {
            $this->cancionesIdcanciones[] = $cancionesIdcancione;
            $cancionesIdcancione->addAutoresIdautore($this);
        }

        return $this;
    }

    public function removeCancionesIdcancione(Canciones $cancionesIdcancione): self
    {
        if ($this->cancionesIdcanciones->contains($cancionesIdcancione)) {
            $this->cancionesIdcanciones->removeElement($cancionesIdcancione);
            $cancionesIdcancione->removeAutoresIdautore($this);
        }

        return $this;
    }

    /**
     * @return Collection|Etiquetas[]
     */
    public function getEtiquetasIdetiquetas(): Collection
    {
        return $this->etiquetasIdetiquetas;
    }

    public function addEtiquetasIdetiqueta(Etiquetas $etiquetasIdetiqueta): self
    {
        if (!$this->etiquetasIdetiquetas->contains($etiquetasIdetiqueta)) {
            $this->etiquetasIdetiquetas[] = $etiquetasIdetiqueta;
            $etiquetasIdetiqueta->addAutoresIdautore($this);
        }

        return $this;
    }

    public function removeEtiquetasIdetiqueta(Etiquetas $etiquetasIdetiqueta): self
    {
        if ($this->etiquetasIdetiquetas->contains($etiquetasIdetiqueta)) {
            $this->etiquetasIdetiquetas->removeElement($etiquetasIdetiqueta);
            $etiquetasIdetiqueta->removeAutoresIdautore($this);
        }

        return $this;
    }

}
