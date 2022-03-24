<?php

namespace App\Entity\DispatchSpace;



use App\Entity\LogMessages\MsgWebsite;
use App\Entity\LogMessages\PrivateConvers;
use App\Entity\LogMessages\PublicationConvers;
use App\Entity\Users\Contacts;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_allmessagesdispatch")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\TballmessageRepository")
 */
class Tballmessage
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\OneToOne(targetEntity="App\Entity\LogMessages\PublicationConvers")
     * @ORM\JoinColumn(nullable=true)
     */
    private $tballmsgp;

    /**
     *
     * @ORM\OneToOne(targetEntity="App\Entity\LogMessages\PrivateConvers")
     * @ORM\JoinColumn(nullable=true)
     */
    private $tballmsgd;

    /**
     *
     * @ORM\OneToOne(targetEntity="App\Entity\LogMessages\MsgWebsite")
     * @ORM\JoinColumn(nullable=true)
     */
    private $tballmsgs;

    /**
     * @ORM\ManyToOne(targetEntity="\App\Entity\DispatchSpace\DispatchSpaceWeb", inversedBy="allmessages", cascade={"persist", "remove"}))
     */
    private $dispatch;

    /**
     * @ORM\ManyToOne(targetEntity="\App\Entity\Users\Contacts", inversedBy="allmessages",cascade={"persist", "remove"})
     */
    private $contact;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastsender;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $extract;

    /**
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastconvers;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDispatch(): ?DispatchSpaceWeb
    {
        return $this->dispatch;
    }

    public function setDispatch(?DispatchSpaceWeb $dispatch): self
    {
        $this->dispatch = $dispatch;

        return $this;
    }

    public function getContact(): ?Contacts
    {
        return $this->contact;
    }

    public function setContact(?Contacts $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function getTballmsgp(): ?PublicationConvers
    {
        return $this->tballmsgp;
    }

    public function setTballmsgp(?PublicationConvers $tballmsgp): self
    {
        $this->tballmsgp = $tballmsgp;

        return $this;
    }

    public function getTballmsgd(): ?PrivateConvers
    {
        return $this->tballmsgd;
    }

    public function setTballmsgd(?PrivateConvers $tballmsgd): self
    {
        $this->tballmsgd = $tballmsgd;

        return $this;
    }

    public function getTballmsgs(): ?MsgWebsite
    {
        return $this->tballmsgs;
    }

    public function setTballmsgs(?MsgWebsite $tballmsgs): self
    {
        $this->tballmsgs = $tballmsgs;

        return $this;
    }

    public function getLastsender(): ?string
    {
        return $this->lastsender;
    }

    public function setLastsender(?string $lastsender): self
    {
        $this->lastsender = $lastsender;

        return $this;
    }

    public function getExtract(): ?string
    {
        return $this->extract;
    }

    public function setExtract(?string $extract): self
    {
        $this->extract = $extract;

        return $this;
    }
    public function getLastconvers(): ?\DateTimeInterface
    {
        return $this->lastconvers;
    }

    public function setLastconvers(?\DateTimeInterface $lastconvers): self
    {
        $this->lastconvers = $lastconvers;

        return $this;
    }

}