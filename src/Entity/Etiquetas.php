<?php

namespace App\Entity;

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
     * @var bool|null
     *
     * @ORM\Column(name="tipoAutor", type="boolean", nullable=true)
     */
    private $tipoautor = '0';

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

    public function getTipoautor(): ?bool
    {
        return $this->tipoautor;
    }

    public function setTipoautor(?bool $tipoautor): self
    {
        $this->tipoautor = $tipoautor;

        return $this;
    }


}
