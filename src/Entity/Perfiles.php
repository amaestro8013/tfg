<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Perfiles
 *
 * @ORM\Table(name="Perfiles", indexes={@ORM\Index(name="fk_Perfiles_Foros1_idx", columns={"Foros_idForos"}), @ORM\Index(name="fk_Perfiles_Usuarios1_idx", columns={"Usuarios_idUsuarios"})})
 * @ORM\Entity
 */
class Perfiles
{
    /**
     * @var int
     *
     * @ORM\Column(name="idPerfiles", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idperfiles;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=45, nullable=false)
     */
    private $nombre;

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

    public function getIdperfiles(): ?int
    {
        return $this->idperfiles;
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
