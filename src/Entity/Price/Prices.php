<?php

namespace App\Entity\Price;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="aff_prices")
 * @ORM\Entity()
 */

class Prices
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $priceunit;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=125, nullable=true)
     */
    private $infoprice;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $free=false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($null)
    {
        $this->id=null;
        return $this;
    }

    public function getPriceunit(): ?float
    {
        return $this->priceunit;
    }

    public function setPriceunit(?float $priceunit): self
    {
        $this->priceunit = $priceunit;

        return $this;
    }

    public function getInfoprice(): ?string
    {
        return $this->infoprice;
    }

    public function setInfoprice(?string $infoprice): self
    {
        $this->infoprice = $infoprice;

        return $this;
    }

    public function getFree(): ?bool
    {
        return $this->free;
    }

    public function setFree(bool $free): self
    {
        $this->free = $free;

        return $this;
    }
}