<?php


namespace App\Entity\Admin;

use App\Entity\Agenda\Subscription;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="aff_wborderproduct")
 * @ORM\Entity()
 */
class WbOrderProducts
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Admin\Wborders", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Admin\Products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $multiple;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $priceht;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $remise=false;

    /**
     * @ORM\OneToOne(targetEntity="\App\Entity\Agenda\Subscription", mappedBy="wbprodorder", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $subscription;

    /**
     * @var boolean
     *
     * @ORM\Column(name="valider", type="boolean", options={"default":false})
     */
    private $valider=false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMultiple(): ?float
    {
        return $this->multiple;
    }

    public function setMultiple(float $multiple): self
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function getPriceht(): ?float
    {
        return $this->priceht;
    }

    public function setPriceht(?float $priceht): self
    {
        $this->priceht = $priceht;

        return $this;
    }

    public function setRemise(bool $remise): self
    {
        $this->remise = $remise;

        return $this;
    }

    public function isRemised(): ?bool
    {
        return $this->remise;
    }

    public function getOrder(): ?Wborders
    {
        return $this->order;
    }

    public function setOrder(?Wborders $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getProduct(): ?Products
    {
        return $this->product;
    }

    public function setProduct(?Products $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(?Subscription $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRemise(): ?bool
    {
        return $this->remise;
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

}