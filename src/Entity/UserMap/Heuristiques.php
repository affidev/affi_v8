<?php

namespace App\Entity\UserMap;


use App\Entity\Customer\Customers;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="aff_heuristique")
 * @ORM\Entity()
 */
class Heuristiques
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer\Customers", inversedBy="heuristique")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    /**
     * @var string $sem
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sem;

    /**
     * @var string $color
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $color;

    /**
     * @var datetime $dateinsert
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $dateinsert;

    /**
     * @var string $binarycolor
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $binarycolor;


    public function __construct($customer)
    {
        $this->dateinsert=new DateTime();
        $this->customer=$customer;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSem(): ?string
    {
        return $this->sem;
    }

    public function setSem(?string $sem): self
    {
        $this->sem = $sem;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getDateinsert(): ?\DateTimeInterface
    {
        return $this->dateinsert;
    }

    public function setDateinsert(\DateTimeInterface $dateinsert): self
    {
        $this->dateinsert = $dateinsert;

        return $this;
    }

    public function getBinarycolor(): ?string
    {
        return $this->binarycolor;
    }

    public function setBinarycolor(?string $binarycolor): self
    {
        $this->binarycolor = $binarycolor;

        return $this;
    }

    public function getCustomer(): ?Customers
    {
        return $this->customer;
    }

    public function setCustomer(?Customers $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

}