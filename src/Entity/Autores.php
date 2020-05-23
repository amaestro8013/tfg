<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Autores
 *
 * @ORM\Table(name="Autores", indexes={@ORM\Index(name="fk_Autores_Etiquetas1_idx", columns={"Etiquetas_idEtiquetas"})})
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
     * @var \Etiquetas
     *
     * @ORM\ManyToOne(targetEntity="Etiquetas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Etiquetas_idEtiquetas", referencedColumnName="idEtiquetas")
     * })
     */
    private $etiquetasIdetiquetas;

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
