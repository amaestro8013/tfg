<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Forosetiquetas
 *
 * @ORM\Table(name="ForosEtiquetas", indexes={@ORM\Index(name="fk_ForoEtiquetas_Etiquetas1_idx", columns={"Etiquetas_idEtiquetas"}), @ORM\Index(name="fk_ForoEtiquetas_Foros1_idx", columns={"Foros_idForos"})})
 * @ORM\Entity
 */
class Forosetiquetas
{
    /**
     * @var int
     *
     * @ORM\Column(name="idForoEtiquetas", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idforoetiquetas;

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
     * @var \Foros
     *
     * @ORM\ManyToOne(targetEntity="Foros")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Foros_idForos", referencedColumnName="idForos")
     * })
     */
    private $forosIdforos;

    public function getIdforoetiquetas(): ?int
    {
        return $this->idforoetiquetas;
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

    public function getForosIdforos(): ?Foros
    {
        return $this->forosIdforos;
    }

    public function setForosIdforos(?Foros $forosIdforos): self
    {
        $this->forosIdforos = $forosIdforos;

        return $this;
    }


}
