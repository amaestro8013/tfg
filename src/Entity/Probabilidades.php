<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Probabilidades
 *
 * @ORM\Table(name="Probabilidades")
 * @ORM\Entity
 */
class Probabilidades
{
    /**
     * @var int
     *
     * @ORM\Column(name="idProbabilidades", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idprobabilidades;

    /**
     * @var int
     *
     * @ORM\Column(name="idPerfil", type="integer", nullable=false)
     */
    private $idperfil;

    /**
     * @var int
     *
     * @ORM\Column(name="idEtiqueta", type="integer", nullable=false)
     */
    private $idetiqueta;

    /**
     * @var int
     *
     * @ORM\Column(name="tipo", type="integer", nullable=false)
     */
    private $tipo;

    /**
     * @var float
     *
     * @ORM\Column(name="Probabilidad", type="float", precision=10, scale=0, nullable=false)
     */
    private $probabilidad;

    public function getIdprobabilidades(): ?int
    {
        return $this->idprobabilidades;
    }

    public function getIdperfil(): ?int
    {
        return $this->idperfil;
    }

    public function setIdperfil(int $idperfil): self
    {
        $this->idperfil = $idperfil;

        return $this;
    }

    public function getIdetiqueta(): ?int
    {
        return $this->idetiqueta;
    }

    public function setIdetiqueta(int $idetiqueta): self
    {
        $this->idetiqueta = $idetiqueta;

        return $this;
    }

    public function getTipo(): ?int
    {
        return $this->tipo;
    }

    public function setTipo(int $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getProbabilidad(): ?float
    {
        return $this->probabilidad;
    }

    public function setProbabilidad(float $probabilidad): self
    {
        $this->probabilidad = $probabilidad;

        return $this;
    }


}
