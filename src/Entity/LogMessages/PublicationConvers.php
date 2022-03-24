<?php


namespace App\Entity\LogMessages;


use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\DispatchSpace\Tballmessage;
use App\Entity\Module\TabpublicationMsgs;
use App\Entity\Users\Contacts;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_publicationconvers")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\PublicationConversRepository")
 */
class PublicationConvers
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $isdispatchsender = false;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $isclient = false;

    /**
     * @ORM\OneToOne(targetEntity="\App\Entity\DispatchSpace\Tballmessage", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $tballmsgp;


    /**
     * @ORM\ManyToOne(targetEntity="\App\Entity\Module\TabpublicationMsgs", inversedBy="idmessage")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tabpublication;

    /**
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $create_at;

    /**
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateclosed;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\LogMessages\MsgsP", mappedBy="publicationmsg", cascade={"persist", "remove"})
     */
    private $msgs;

    /**
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $sender;


    public function __construct()
    {
        $this->create_at=new DateTime();
        $this->msgs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsdispatchsender(): ?bool
    {
        return $this->isdispatchsender;
    }

    public function setIsdispatchsender(bool $isdispatchsender): self
    {
        $this->isdispatchsender = $isdispatchsender;

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

    public function getDateclosed(): ?\DateTimeInterface
    {
        return $this->dateclosed;
    }

    public function setDateclosed(?\DateTimeInterface $dateclosed): self
    {
        $this->dateclosed = $dateclosed;

        return $this;
    }

    public function getTballmsgp(): ?Tballmessage
    {
        return $this->tballmsgp;
    }

    public function setTballmsgp(?Tballmessage $tballmsgp): self
    {
        $this->tballmsgp = $tballmsgp;

        return $this;
    }

    public function getTabpublication(): ?TabpublicationMsgs
    {
        return $this->tabpublication;
    }

    public function setTabpublication(?TabpublicationMsgs $tabpublication): self
    {
        $this->tabpublication = $tabpublication;

        return $this;
    }

    /**
     * @return Collection|MsgsP[]
     */
    public function getMsgs(): Collection
    {
        return $this->msgs;
    }

    public function addMsg(MsgsP $msg): self
    {
        if (!$this->msgs->contains($msg)) {
            $this->msgs[] = $msg;
            $msg->setPublicationmsg($this);
        }

        return $this;
    }

    public function removeMsg(MsgsP $msg): self
    {
        if ($this->msgs->removeElement($msg)) {
            // set the owning side to null (unless already changed)
            if ($msg->getPublicationmsg() === $this) {
                $msg->setPublicationmsg(null);
            }
        }

        return $this;
    }

    public function getSender(): ?bool
    {
        return $this->sender;
    }

    public function setSender(?bool $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getIsclient(): ?bool
    {
        return $this->isclient;
    }

    public function setIsclient(bool $isclient): self
    {
        $this->isclient = $isclient;

        return $this;
    }

}