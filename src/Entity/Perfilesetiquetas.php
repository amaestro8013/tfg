<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Perfilesetiquetas
 *
 * @ORM\Table(name="PerfilesEtiquetas", indexes={@ORM\Index(name="fk_PerfilesEtiquetas_Etiquetas1_idx", columns={"Etiquetas_idEtiquetas"}), @ORM\Index(name="fk_PerfilesEtiquetas_Perfiles1_idx", columns={"Perfiles_idPerfiles"})})
 * @ORM\Entity
 */
class Perfilesetiquetas
{
    /**
     * @var int
     *
     * @ORM\Column(name="idPerfilesEtiquetas", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idperfilesetiquetas;

    /**
     * @var \Etiquetas
     *
     * @ORM\ManyToOne(targetEntity="Etiquetas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Etiquetas_idEtiquetas", referencedColumnName="idEtiquetas")
     * })
     */
    private $etiquetasIdetiquetas;

    /**
     * @var \Perfiles
     *
     * @ORM\ManyToOne(targetEntity="Perfiles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Perfiles_idPerfiles", referencedColumnName="idPerfiles")
     * })
     */
    private $perfilesIdperfiles;

    public function getIdperfilesetiquetas(): ?int
    {
        return $this->idperfilesetiquetas;
    }

    public function getEtiquetasIdetiquetas(): ?Etiquetas
    {
        return $this->etiquetasIdetiquetas;
    }

    public function setEtiquetasIdetiquetas(?Etiquetas $etiquetasIdetiquetas): self
    {
        $this->etiquetasIdetiquetas = $etiquetasIdetiquetas;

        return $this;
    }

    public function getPerfilesIdperfiles(): ?Perfiles
    {
        return $this->perfilesIdperfiles;
    }

    public function setPerfilesIdperfiles(?Perfiles $perfilesIdperfiles): self
    {
        $this->perfilesIdperfiles = $perfilesIdperfiles;

        return $this;
    }


}
