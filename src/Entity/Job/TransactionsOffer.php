<?php


namespace App\Entity\Job;

use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\LogMessages\PrivateConvers;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="aff_transactionsoffer")
 * @ORM\Entity()
 */
class TransactionsOffer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Job\Offer", inversedBy="transactions")
     * @ORM\JoinColumn(nullable=true)
     */
    private $offer;

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
     * @ORM\OneToOne(targetEntity="App\Entity\DispatchSpace\DispatchSpaceWeb")
     * @ORM\JoinColumn(nullable=true)
     */
    private $client;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endAt;

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
    private $closed = false;

    /**
     * @ORM\Column(type="string", length=250)
     */
    private $motifclosed;

    public function __construct()
    {
        $this->create_at=new DateTimeImmutable();
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

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

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

    public function setMotifclosed(string $motifclosed): self
    {
        $this->motifclosed = $motifclosed;

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

    public function getClient(): ?DispatchSpaceWeb
    {
        return $this->client;
    }

    public function setClient(?DispatchSpaceWeb $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getOffer(): ?Offer
    {
        return $this->offer;
    }

    public function setOffer(?Offer $offer): self
    {
        $this->offer = $offer;

        return $this;
    }

}