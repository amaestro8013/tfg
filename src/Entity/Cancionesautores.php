<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cancionesautores
 *
 * @ORM\Table(name="CancionesAutores", indexes={@ORM\Index(name="fk_CancionesAutores_Autores1_idx", columns={"Autores_idAutores"}), @ORM\Index(name="fk_CancionesAutores_Canciones1_idx", columns={"Canciones_idCanciones"})})
 * @ORM\Entity
 */
class Cancionesautores
{
    /**
     * @var int
     *
     * @ORM\Column(name="idCancionesAutores", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idcancionesautores;

    /**
     * @var \Autores
     *
     * @ORM\ManyToOne(targetEntity="Autores")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Autores_idAutores", referencedColumnName="idAutores")
     * })
     */
    private $autoresIdautores;

    /**
     * @var \Canciones
     *
     * @ORM\ManyToOne(targetEntity="Canciones")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Canciones_idCanciones", referencedColumnName="idCanciones")
     * })
     */
    private $cancionesIdcanciones;

    public function getIdcancionesautores(): ?int
    {
        return $this->idcancionesautores;
    }

    public function getAutoresIdautores(): ?Autores
    {
        return $this->autoresIdautores;
    }

    public function setAutoresIdautores(?Autores $autoresIdautores): self
    {
        $this->autoresIdautores = $autoresIdautores;

        return $this;
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


}
