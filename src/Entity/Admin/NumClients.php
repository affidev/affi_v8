<?php


namespace App\Entity\Admin;


use App\Entity\Customer\Customers;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="aff_numclient")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\NumClientsRepository")
 * @UniqueEntity("numero")
 */
class NumClients
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
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $numero;

    /**
     * @var float
     *
     * @ORM\Column(name="ordre", type="float")
     */
    private $ordre;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Customer\Customers")
     * @ORM\JoinColumn()
     */
    private $idcustomer;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Admin\Orders", mappedBy="numclient" , cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function increaseClient()
    {
        $this->numero++;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getOrdre(): ?float
    {
        return $this->ordre;
    }

    public function setOrdre(float $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Orders $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setNumclient($this);
        }

        return $this;
    }

    public function removeOrder(Orders $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getNumclient() === $this) {
                $order->setNumclient(null);
            }
        }

        return $this;
    }

    public function getIdcustomer(): ?Customers
    {
        return $this->idcustomer;
    }

    public function setIdcustomer(?Customers $idcustomer): self
    {
        $this->idcustomer = $idcustomer;

        return $this;
    }

}