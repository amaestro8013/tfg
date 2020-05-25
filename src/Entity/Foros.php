<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Foros
 *
 * @ORM\Table(name="Foros")
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

    public function getIdforos(): ?int
    {
        return $this->idforos;
    }


}
