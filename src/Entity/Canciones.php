<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Canciones
 *
 * @ORM\Table(name="Canciones")
 * @ORM\Entity
 */
class Canciones
{
    /**
     * @var int
     *
     * @ORM\Column(name="idCanciones", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idcanciones;

    /**
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=45, nullable=false)
     */
    private $titulo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="duracion", type="string", length=45, nullable=true)
     */
    private $duracion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="album", type="string", length=45, nullable=true)
     */
    private $album;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fecha", type="string", length=45, nullable=true)
     */
    private $fecha;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Autores", inversedBy="cancionesIdcanciones")
     * @ORM\JoinTable(name="canciones_has_autores",
     *   joinColumns={
     *     @ORM\JoinColumn(name="Canciones_idCanciones", referencedColumnName="idCanciones")
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
     * @ORM\ManyToMany(targetEntity="Etiquetas", inversedBy="cancionesIdcanciones")
     * @ORM\JoinTable(name="canciones_has_etiquetas",
     *   joinColumns={
     *     @ORM\JoinColumn(name="Canciones_idCanciones", referencedColumnName="idCanciones")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="Etiquetas_idEtiquetas", referencedColumnName="idEtiquetas")
     *   }
     * )
     */
    private $etiquetasIdetiquetas;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->autoresIdautores = new \Doctrine\Common\Collections\ArrayCollection();
        $this->etiquetasIdetiquetas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getIdcanciones(): ?int
    {
        return $this->idcanciones;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getDuracion(): ?string
    {
        return $this->duracion;
    }

    public function setDuracion(?string $duracion): self
    {
        $this->duracion = $duracion;

        return $this;
    }

    public function getAlbum(): ?string
    {
        return $this->album;
    }

    public function setAlbum(?string $album): self
    {
        $this->album = $album;

        return $this;
    }

    public function getFecha(): ?string
    {
        return $this->fecha;
    }

    public function setFecha(?string $fecha): self
    {
        $this->fecha = $fecha;

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
        }

        return $this;
    }

    public function removeEtiquetasIdetiqueta(Etiquetas $etiquetasIdetiqueta): self
    {
        if ($this->etiquetasIdetiquetas->contains($etiquetasIdetiqueta)) {
            $this->etiquetasIdetiquetas->removeElement($etiquetasIdetiqueta);
        }

        return $this;
    }

}
