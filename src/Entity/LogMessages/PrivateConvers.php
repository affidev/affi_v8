<?php


namespace App\Entity\LogMessages;

use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\DispatchSpace\Tballmessage;
use App\Entity\Websites\Website;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_privateconvers")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\PrivateConversRepository")
 */
class PrivateConvers
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToMany(targetEntity="\App\Entity\DispatchSpace\DispatchSpaceWeb")
     */
    private $dispatchdest;

    /**
     * @ORM\ManyToOne(targetEntity="\App\Entity\DispatchSpace\DispatchSpaceWeb")
     */
    private $dispatchopen;

    /**
     * @ORM\OneToOne(targetEntity="\App\Entity\DispatchSpace\Tballmessage", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $tballmsgd;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=false)
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
    private $dateclosed;

    /**
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $sender;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\LogMessages\MsgsD", mappedBy="conversprivate", cascade={"persist", "remove"})
     */
    private $msgs;


    public function __construct()
    {
        $this->create_at=new DateTime();
        $this->dispatchdest = new ArrayCollection();
        $this->msgs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
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

    public function getDateclosed(): ?\DateTimeInterface
    {
        return $this->dateclosed;
    }

    public function setDateclosed(?\DateTimeInterface $dateclosed): self
    {
        $this->dateclosed = $dateclosed;

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

    /**
     * @return Collection|DispatchSpaceWeb[]
     */
    public function getDispatchdest(): Collection
    {
        return $this->dispatchdest;
    }

    public function addDispatchdest(DispatchSpaceWeb $dispatchdest): self
    {
        if (!$this->dispatchdest->contains($dispatchdest)) {
            $this->dispatchdest[] = $dispatchdest;
        }

        return $this;
    }

    public function removeDispatchdest(DispatchSpaceWeb $dispatchdest): self
    {
        $this->dispatchdest->removeElement($dispatchdest);

        return $this;
    }

    public function getDispatchopen(): ?DispatchSpaceWeb
    {
        return $this->dispatchopen;
    }

    public function setDispatchopen(?DispatchSpaceWeb $dispatchopen): self
    {
        $this->dispatchopen = $dispatchopen;

        return $this;
    }

    public function getTballmsgd(): ?Tballmessage
    {
        return $this->tballmsgd;
    }

    public function setTballmsgd(?Tballmessage $tballmsgd): self
    {
        $this->tballmsgd = $tballmsgd;

        return $this;
    }

    /**
     * @return Collection|MsgsD[]
     */
    public function getMsgs(): Collection
    {
        return $this->msgs;
    }

    public function addMsg(MsgsD $msg): self
    {
        if (!$this->msgs->contains($msg)) {
            $this->msgs[] = $msg;
            $msg->setConversprivate($this);
        }

        return $this;
    }

    public function removeMsg(MsgsD $msg): self
    {
        if ($this->msgs->removeElement($msg)) {
            // set the owning side to null (unless already changed)
            if ($msg->getConversprivate() === $this) {
                $msg->setConversprivate(null);
            }
        }

        return $this;
    }

}