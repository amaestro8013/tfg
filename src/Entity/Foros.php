<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Foros
 *
 * @ORM\Table(name="Foros", indexes={@ORM\Index(name="fk_Foros_Perfiles1_idx", columns={"Perfiles_idPerfiles"})})
 * @ORM\Entity
 */
class Foros
{
    /**
     * @var int
     *
     * @ORM\Column(name="idForos", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idforos;

    /**
     * @var \Perfiles
     *
     * @ORM\ManyToOne(targetEntity="Perfiles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Perfiles_idPerfiles", referencedColumnName="idPerfiles")
     * })
     */
    private $perfilesIdperfiles;

    public function getIdforos(): ?int
    {
        return $this->idforos;
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
