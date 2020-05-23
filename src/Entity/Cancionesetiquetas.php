<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cancionesetiquetas
 *
 * @ORM\Table(name="CancionesEtiquetas", indexes={@ORM\Index(name="fk_CancionesEtiquetas_Canciones1_idx", columns={"Canciones_idCanciones"}), @ORM\Index(name="fk_CancionesEtiquetas_Etiquetas1_idx", columns={"Etiquetas_idEtiquetas"})})
 * @ORM\Entity
 */
class Cancionesetiquetas
{
    /**
     * @var int
     *
     * @ORM\Column(name="idCancionesEtiquetas", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idcancionesetiquetas;

    /**
     * @var \Canciones
     *
     * @ORM\ManyToOne(targetEntity="Canciones")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Canciones_idCanciones", referencedColumnName="idCanciones")
     * })
     */
    private $cancionesIdcanciones;

    /**
     * @var \Etiquetas
     *
     * @ORM\ManyToOne(targetEntity="Etiquetas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Etiquetas_idEtiquetas", referencedColumnName="idEtiquetas")
     * })
     */
    private $etiquetasIdetiquetas;

    public function getIdcancionesetiquetas(): ?int
    {
        return $this->idcancionesetiquetas;
    }

    public function getCancionesIdcanciones(): ?Canciones
    {
        return $this->cancionesIdcanciones;
    }

    public function setCancionesIdcanciones(?Canciones $cancionesIdcanciones): self
    {
        $this->cancionesIdcanciones = $cancionesIdcanciones;

        return $this;
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


}
