<?php

namespace App\Entity\Customer;

use App\Entity\Admin\Orders;
use App\Entity\Module\Formules;
use App\Entity\LogMessages\PrivateConvers;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="aff_cmdformule")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\CommandeFormuleRepository")
 */
class CommandeFormule
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Module\Formules", inversedBy="commandes")
     * @ORM\JoinColumn(nullable=true)
     */
    private $formule;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Admin\Orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $order;

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
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $create_at;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\LogMessages\PrivateConvers")
     * @ORM\JoinColumn(nullable=true)
     */
    private $convers;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateliv;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $modifAt;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $active = true;


    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $delivred = false;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $closed = false;

    /**
     * @ORM\Column(type="string", length=250, nullable=true))
     */
    private $motifclosed;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $resaconfirmed=false;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $resarappel=false;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $resasend=false;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $resaread=false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logresa=false;


    public function getId(): ?int
    {
        return $this->id;
    }
    public function __construct()
    {
        $this->create_at=new DateTimeImmutable();
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

    public function setRemise(bool $remise): self
    {
        $this->remise = $remise;

        return $this;
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

    public function getDateliv(): ?\DateTimeInterface
    {
        return $this->dateliv;
    }

    public function setDateliv(\DateTimeInterface $dateliv): self
    {
        $this->dateliv = $dateliv;

        return $this;
    }

    public function getModifAt(): ?\DateTimeInterface
    {
        return $this->modifAt;
    }

    public function setModifAt(?\DateTimeInterface $modifAt): self
    {
        $this->modifAt = $modifAt;

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

    public function getDelivred(): ?bool
    {
        return $this->delivred;
    }

    public function setDelivred(bool $delivred): self
    {
        $this->delivred = $delivred;

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

    public function getMotifclosed(): ?string
    {
        return $this->motifclosed;
    }

    public function setMotifclosed(?string $motifclosed): self
    {
        $this->motifclosed = $motifclosed;

        return $this;
    }

    public function getResaconfirmed(): ?bool
    {
        return $this->resaconfirmed;
    }

    public function setResaconfirmed(bool $resaconfirmed): self
    {
        $this->resaconfirmed = $resaconfirmed;

        return $this;
    }

    public function getResarappel(): ?bool
    {
        return $this->resarappel;
    }

    public function setResarappel(bool $resarappel): self
    {
        $this->resarappel = $resarappel;

        return $this;
    }

    public function getResasend(): ?bool
    {
        return $this->resasend;
    }

    public function setResasend(bool $resasend): self
    {
        $this->resasend = $resasend;

        return $this;
    }

    public function getResaread(): ?bool
    {
        return $this->resaread;
    }

    public function setResaread(bool $resaread): self
    {
        $this->resaread = $resaread;

        return $this;
    }

    public function getLogresa(): ?string
    {
        return $this->logresa;
    }

    public function setLogresa(?string $logresa): self
    {
        $this->logresa = $logresa;

        return $this;
    }

    public function getFormule(): ?Formules
    {
        return $this->formule;
    }

    public function setFormule(?Formules $formule): self
    {
        $this->formule = $formule;

        return $this;
    }

    public function getOrder(): ?Orders
    {
        return $this->order;
    }

    public function setOrder(?Orders $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getConvers(): ?PrivateConvers
    {
        return $this->convers;
    }

    public function setConvers(?PrivateConvers $convers): self
    {
        $this->convers = $convers;

        return $this;
    }
}