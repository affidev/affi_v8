<?php


namespace App\Entity\Admin;

use App\Entity\Websites\Website;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="aff_wbcustomers")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\WbcustomersRepository")
 * @UniqueEntity("numero")
 */
class Wbcustomers
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
     * @ORM\OneToOne(targetEntity="App\Entity\Websites\Website")
     * @ORM\JoinColumn()
     */
    private $website;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Admin\Wborders", mappedBy="wbcustomer" , cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
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

    public function getWebsite(): ?Website
    {
        return $this->website;
    }

    public function setWebsite(?Website $website): self
    {
        $this->website = $website;

        return $this;
    }

    /**
     * @return Collection|Wborders[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Wborders $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setWbcustomer($this);
        }

        return $this;
    }

    public function removeOrder(Wborders $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getWbcustomer() === $this) {
                $order->setWbcustomer(null);
            }
        }

        return $this;
    }

}