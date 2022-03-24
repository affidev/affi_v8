<?php

namespace App\Entity\Admin;


use App\Entity\Customer\Avantages;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 *
 * @ORM\Table(name="aff_Wborders")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\WbordersRepository")
 * @UniqueEntity("numcommande")
 */
class Wborders
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Admin\Wbcustomers", inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $wbcustomer;

    /**
     * @var boolean
     *
     * @ORM\Column(name="valider", type="boolean", options={"default":false})
     */
    private $valider=false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modifdate", type="datetime", nullable=true)
     */
    private $modifdate;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $numcommande;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Admin\WbOrderProducts", mappedBy="order", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $products;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $state;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Customer\Avantages")
     * @ORM\JoinColumn(nullable=true)
     */
    private $avantage;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=false)
     */
    private $totalht;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=false)
     */
    private $totalttc;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=false)
     */
    private $totaltva;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $periodicity= false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $encours=false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startprd;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endprd;

    /**
     * @ORM\Column(type="string",length=5, nullable=true)
     */
    private $periodefac;

    /**
     *
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $dayfact;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $acceptorder=false;


    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->date=new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValider(): ?bool
    {
        return $this->valider;
    }

    public function setValider(bool $valider): self
    {
        $this->valider = $valider;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getModifdate(): ?\DateTimeInterface
    {
        return $this->modifdate;
    }

    public function setModifdate(?\DateTimeInterface $modifdate): self
    {
        $this->modifdate = $modifdate;

        return $this;
    }

    public function getNumcommande(): ?int
    {
        return $this->numcommande;
    }

    public function setNumcommande(int $numcommande): self
    {
        $this->numcommande = $numcommande;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getTotalht(): ?float
    {
        return $this->totalht;
    }

    public function setTotalht(float $totalht): self
    {
        $this->totalht = $totalht;

        return $this;
    }

    public function getTotalttc(): ?float
    {
        return $this->totalttc;
    }

    public function setTotalttc(float $totalttc): self
    {
        $this->totalttc = $totalttc;

        return $this;
    }

    public function getTotaltva(): ?float
    {
        return $this->totaltva;
    }

    public function setTotaltva(float $totaltva): self
    {
        $this->totaltva = $totaltva;

        return $this;
    }

    public function getWbcustomer(): ?Wbcustomers
    {
        return $this->wbcustomer;
    }

    public function setWbcustomer(?Wbcustomers $wbcustomer): self
    {
        $this->wbcustomer = $wbcustomer;

        return $this;
    }

    public function getAvantage(): ?Avantages
    {
        return $this->avantage;
    }

    public function setAvantage(?Avantages $avantage): self
    {
        $this->avantage = $avantage;

        return $this;
    }

    public function getEncours(): ?bool
    {
        return $this->encours;
    }

    public function setEncours(bool $encours): self
    {
        $this->encours = $encours;

        return $this;
    }

    public function getPeriodefac(): ?string
    {
        return $this->periodefac;
    }

    public function setPeriodefac(?string $periodefac): self
    {
        $this->periodefac = $periodefac;

        return $this;
    }

    public function getDayfact(): ?string
    {
        return $this->dayfact;
    }

    public function setDayfact(?string $dayfact): self
    {
        $this->dayfact = $dayfact;

        return $this;
    }

    public function getAcceptorder(): ?bool
    {
        return $this->acceptorder;
    }

    public function setAcceptorder(bool $acceptorder): self
    {
        $this->acceptorder = $acceptorder;

        return $this;
    }

    public function getStartprd(): ?\DateTimeInterface
    {
        return $this->startprd;
    }

    public function setStartprd(?\DateTimeInterface $startprd): self
    {
        $this->startprd = $startprd;

        return $this;
    }

    public function getEndprd(): ?\DateTimeInterface
    {
        return $this->endprd;
    }

    public function setEndprd(?\DateTimeInterface $endprd): self
    {
        $this->endprd = $endprd;

        return $this;
    }

    /**
     * @return Collection|WbOrderProducts[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(WbOrderProducts $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setOrder($this);
        }

        return $this;
    }

    public function removeProduct(WbOrderProducts $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getOrder() === $this) {
                $product->setOrder(null);
            }
        }

        return $this;
    }

    public function getPeriodicity(): ?bool
    {
        return $this->periodicity;
    }

    public function setPeriodicity(bool $periodicity): self
    {
        $this->periodicity = $periodicity;

        return $this;
    }

}