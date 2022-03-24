<?php


namespace App\Entity\Customer;

use App\Entity\Admin\Products;
use DateTime;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_services")
 * @ORM\Entity(repositoryClass="App\Repository\ServicesRepository")
 */
class Services
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne (targetEntity="App\Entity\Admin\Products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $products;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $namemodule;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer\Customers", inversedBy="services")
     */
    private $customer;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datestart_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateend_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datemaj_at;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $active = true;

    public function __construct()
    {
        $this->create_at=new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->create_at;
    }

    public function setCreateAt(\DateTimeInterface $create_at): self
    {
        $this->create_at = $create_at;

        return $this;
    }

    public function getDatestartAt(): ?\DateTimeInterface
    {
        return $this->datestart_at;
    }

    public function setDatestartAt(\DateTimeInterface $datestart_at): self
    {
        $this->datestart_at = $datestart_at;

        return $this;
    }

    public function getDateendAt(): ?\DateTimeInterface
    {
        return $this->dateend_at;
    }

    public function setDateendAt(\DateTimeInterface $dateend_at): self
    {
        $this->dateend_at = $dateend_at;

        return $this;
    }

    public function getDatemajAt(): ?\DateTimeInterface
    {
        return $this->datemaj_at;
    }

    public function setDatemajAt(?\DateTimeInterface $datemaj_at): self
    {
        $this->datemaj_at = $datemaj_at;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

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

    public function getNamemodule(): ?string
    {
        return $this->namemodule;
    }

    public function setNamemodule(string $namemodule): self
    {
        $this->namemodule = $namemodule;

        return $this;
    }

    public function getProducts(): ?Products
    {
        return $this->products;
    }

    public function setProducts(?Products $products): self
    {
        $this->products = $products;

        return $this;
    }

}