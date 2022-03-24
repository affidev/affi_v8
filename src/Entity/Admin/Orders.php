<?php

namespace App\Entity\Admin;


use App\Entity\Customer\Avantages;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 *
 * @ORM\Table(name="aff_orders")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\OrdersRepository")
 * @UniqueEntity("numcommande")
 */
class Orders
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Admin\NumClients", inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $numclient;

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
     * @ORM\Column(type="string", nullable=true)
     */
    private $state;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Customer\Avantages")
     * @ORM\JoinColumn(nullable=true)
     */
    private $avantage;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Admin\OrderProducts", mappedBy="order", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $listproducts;

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


    public function __construct()
    {
        $this->date=new \DateTime();
        $this->listproducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return strval($this->numcommande);
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

    public function getNumcommande(): ?int
    {
        return $this->numcommande;
    }

    public function setNumcommande(int $numcommande): self
    {
        $this->numcommande = $numcommande;

        return $this;
    }

    public function getNumclient(): ?NumClients
    {
        return $this->numclient;
    }

    public function setNumclient(?NumClients $numclient): self
    {
        $this->numclient = $numclient;

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

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

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

    /**
     * @return Collection|OrderProducts[]
     */
    public function getListproducts(): Collection
    {
        return $this->listproducts;
    }

    public function addListproduct(OrderProducts $listproduct): self
    {
        if (!$this->listproducts->contains($listproduct)) {
            $this->listproducts[] = $listproduct;
            $listproduct->setOrder($this);
        }

        return $this;
    }

    public function removeListproduct(OrderProducts $listproduct): self
    {
        if ($this->listproducts->removeElement($listproduct)) {
            // set the owning side to null (unless already changed)
            if ($listproduct->getOrder() === $this) {
                $listproduct->setOrder(null);
            }
        }

        return $this;
    }

}