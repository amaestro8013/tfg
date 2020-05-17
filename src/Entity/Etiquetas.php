<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Etiquetas
 *
 * @ORM\Table(name="Etiquetas")
 * @ORM\Entity
 */
class Etiquetas
{
    /**
     * @var int
     *
     * @ORM\Column(name="idEtiquetas", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idetiquetas;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=45, nullable=false)
     */
    private $nombre;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Canciones", mappedBy="etiquetasIdetiquetas")
     */
    private $cancionesIdcanciones;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Autores", inversedBy="etiquetasIdetiquetas")
     * @ORM\JoinTable(name="etiquetas_has_autores",
     *   joinColumns={
     *     @ORM\JoinColumn(name="Etiquetas_idEtiquetas", referencedColumnName="idEtiquetas")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="Autores_idAutores", referencedColumnName="idAutores")
     *   }
     * )
     */
    private $autoresIdautores;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Perfiles", mappedBy="etiquetasIdetiquetas")
     */
    private $perfilesIdperfiles;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cancionesIdcanciones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->autoresIdautores = new \Doctrine\Common\Collections\ArrayCollection();
        $this->perfilesIdperfiles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getIdetiquetas(): ?int
    {
        return $this->idetiquetas;
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
            $cancionesIdcancione->addEtiquetasIdetiqueta($this);
        }

        return $this;
    }

    public function removeCancionesIdcancione(Canciones $cancionesIdcancione): self
    {
        if ($this->cancionesIdcanciones->contains($cancionesIdcancione)) {
            $this->cancionesIdcanciones->removeElement($cancionesIdcancione);
            $cancionesIdcancione->removeEtiquetasIdetiqueta($this);
        }

        return $this;
    }

    /**
     * @return Collection|Autores[]
     */
    public function getAutoresIdautores(): Collection
    {
        return $this->autoresIdautores;
    }

    public function addAutoresIdautore(Autores $autoresIdautore): self
    {
        if (!$this->autoresIdautores->contains($autoresIdautore)) {
            $this->autoresIdautores[] = $autoresIdautore;
        }

        return $this;
    }

    public function removeAutoresIdautore(Autores $autoresIdautore): self
    {
        if ($this->autoresIdautores->contains($autoresIdautore)) {
            $this->autoresIdautores->removeElement($autoresIdautore);
        }

        return $this;
    }

    /**
     * @return Collection|Perfiles[]
     */
    public function getPerfilesIdperfiles(): Collection
    {
        return $this->perfilesIdperfiles;
    }

    public function addPerfilesIdperfile(Perfiles $perfilesIdperfile): self
    {
        if (!$this->perfilesIdperfiles->contains($perfilesIdperfile)) {
            $this->perfilesIdperfiles[] = $perfilesIdperfile;
            $perfilesIdperfile->addEtiquetasIdetiqueta($this);
        }

        return $this;
    }

    public function removePerfilesIdperfile(Perfiles $perfilesIdperfile): self
    {
        if ($this->perfilesIdperfiles->contains($perfilesIdperfile)) {
            $this->perfilesIdperfiles->removeElement($perfilesIdperfile);
            $perfilesIdperfile->removeEtiquetasIdetiqueta($this);
        }

        return $this;
    }

}
