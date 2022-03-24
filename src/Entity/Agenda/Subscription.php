<?php

namespace App\Entity\Agenda;

use App\Entity\Admin\OrderProducts;
use App\Entity\Admin\WbOrderProducts;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_subscription")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\SubscriptionRepository")
 */
class Subscription
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $TypeSubscription;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $starttime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endtime;

    /**
     * @ORM\OneToOne(targetEntity="\App\Entity\Admin\WbOrderProducts", inversedBy="subscription")
     * @ORM\JoinColumn(nullable=true)
     */
    private $wbprodorder;

    /**
     * @ORM\OneToOne(targetEntity="\App\Entity\Admin\OrderProducts", inversedBy="subscription")
     * @ORM\JoinColumn(nullable=true)
     */
    private $orderproduct;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $closed=false;

    public function __construct()
    {
        $this->TypeSubscription= 1;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeSubscription(): ?int
    {
        return $this->TypeSubscription;
    }

    public function setTypeSubscription(int $TypeSubscription): self
    {
        $this->TypeSubscription = $TypeSubscription;

        return $this;
    }

    public function getStarttime(): ?\DateTimeInterface
    {
        return $this->starttime;
    }

    public function setStarttime(?\DateTimeInterface $starttime): self
    {
        $this->starttime = $starttime;

        return $this;
    }

    public function getEndtime(): ?\DateTimeInterface
    {
        return $this->endtime;
    }

    public function setEndtime(?\DateTimeInterface $endtime): self
    {
        $this->endtime = $endtime;

        return $this;
    }

    public function getClosed(): ?bool
    {
        return $this->closed;
    }

    public function setClosed(bool $closed): self
    {
        $this->closed = $closed;

        return $this;
    }
    public function getWbprodorder(): ?WbOrderProducts
    {
        return $this->wbprodorder;
    }

    public function setWbprodorder(?WbOrderProducts $wbprodorder): self
    {
        $this->wbprodorder = $wbprodorder;

        return $this;
    }

    public function getOrderproduct(): ?OrderProducts
    {
        return $this->orderproduct;
    }

    public function setOrderproduct(?OrderProducts $orderproduct): self
    {
        $this->orderproduct = $orderproduct;

        return $this;
    }

}
