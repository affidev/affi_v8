<?php


namespace App\Entity\LogMessages;


use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\DispatchSpace\Tballmessage;
use App\Entity\Users\Contacts;
use App\Entity\Websites\Website;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_msgwebsite")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\MsgWebisteRepository")
 */
class MsgWebsite
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
    private $isspaceweb = false;


    /**
     * @ORM\OneToOne(targetEntity="\App\Entity\DispatchSpace\Tballmessage", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $tballmsgs;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $isclient = false;

    /**
     * @ORM\ManyToOne(targetEntity="\App\Entity\Websites\Website", inversedBy="msgs", cascade={"persist","remove"})
     */
    private $websitedest;

    /**
     * @ORM\ManyToOne(targetEntity="\App\Entity\DispatchSpace\DispatchSpaceWeb")
     * @ORM\JoinColumn(nullable=true)
     */
    private $spacewebexpe;

    /**
     *
     * @ORM\ManyToOne(targetEntity="\App\Entity\Users\Contacts")
     * @ORM\JoinColumn(nullable=true)
     */
    private $contactexp;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subject;

    /**
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $create_at;

    /**
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateClosed;

    /**
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $sender;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\LogMessages\Msgs", mappedBy="msgwebsite", cascade={"persist", "remove"})
     */
    private $msgs;

    public function __construct()
    {
        $this->create_at=new DateTime();
        $this->msgs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsspaceweb(): ?bool
    {
        return $this->isspaceweb;
    }

    public function setIsspaceweb(bool $isspaceweb): self
    {
        $this->isspaceweb = $isspaceweb;

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

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

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

    public function getDateClosed(): ?\DateTimeInterface
    {
        return $this->dateClosed;
    }

    public function setDateClosed(?\DateTimeInterface $dateClosed): self
    {
        $this->dateClosed = $dateClosed;

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

    public function getTballmsgs(): ?Tballmessage
    {
        return $this->tballmsgs;
    }

    public function setTballmsgs(?Tballmessage $tballmsgs): self
    {
        $this->tballmsgs = $tballmsgs;

        return $this;
    }

    public function getWebsitedest(): ?Website
    {
        return $this->websitedest;
    }

    public function setWebsitedest(?Website $websitedest): self
    {
        $this->websitedest = $websitedest;

        return $this;
    }

    public function getSpacewebexpe(): ?DispatchSpaceWeb
    {
        return $this->spacewebexpe;
    }

    public function setSpacewebexpe(?DispatchSpaceWeb $spacewebexpe): self
    {
        $this->spacewebexpe = $spacewebexpe;

        return $this;
    }

    public function getContactexp(): ?Contacts
    {
        return $this->contactexp;
    }

    public function setContactexp(?Contacts $contactexp): self
    {
        $this->contactexp = $contactexp;

        return $this;
    }

    /**
     * @return Collection|Msgs[]
     */
    public function getMsgs(): Collection
    {
        return $this->msgs;
    }

    public function addMsg(Msgs $msg): self
    {
        if (!$this->msgs->contains($msg)) {
            $this->msgs[] = $msg;
            $msg->setMsgwebsite($this);
        }

        return $this;
    }

    public function removeMsg(Msgs $msg): self
    {
        if ($this->msgs->removeElement($msg)) {
            // set the owning side to null (unless already changed)
            if ($msg->getMsgwebsite() === $this) {
                $msg->setMsgwebsite(null);
            }
        }

        return $this;
    }

}