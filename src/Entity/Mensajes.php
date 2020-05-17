<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mensajes
 *
 * @ORM\Table(name="Mensajes", indexes={@ORM\Index(name="fk_Mensajes_Foros1_idx", columns={"Foros_idForos"}), @ORM\Index(name="fk_Mensajes_Usuarios1_idx", columns={"Usuarios_idUsuarios"})})
 * @ORM\Entity
 */
class Mensajes
{
    /**
     * @var int
     *
     * @ORM\Column(name="idMensajes", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idmensajes;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comentario", type="string", length=45, nullable=true)
     */
    private $comentario;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fecha", type="string", length=45, nullable=true)
     */
    private $fecha;

    /**
     * @var \Foros
     *
     * @ORM\ManyToOne(targetEntity="Foros")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Foros_idForos", referencedColumnName="idForos")
     * })
     */
    private $forosIdforos;

    /**
     * @var \Usuarios
     *
     * @ORM\ManyToOne(targetEntity="Usuarios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Usuarios_idUsuarios", referencedColumnName="idUsuarios")
     * })
     */
    private $usuariosIdusuarios;

    public function getIdmensajes(): ?int
    {
        return $this->idmensajes;
    }

    public function getComentario(): ?string
    {
        return $this->comentario;
    }

    public function setComentario(?string $comentario): self
    {
        $this->comentario = $comentario;

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

    public function getForosIdforos(): ?Foros
    {
        return $this->forosIdforos;
    }

    public function setForosIdforos(?Foros $forosIdforos): self
    {
        $this->forosIdforos = $forosIdforos;

        return $this;
    }

    public function getUsuariosIdusuarios(): ?Usuarios
    {
        return $this->usuariosIdusuarios;
    }

    public function setUsuariosIdusuarios(?Usuarios $usuariosIdusuarios): self
    {
        $this->usuariosIdusuarios = $usuariosIdusuarios;

        return $this;
    }


}
