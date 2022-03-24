<?php


namespace App\Entity\LogMessages;


use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\Notifications\Notifdispatch;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_tabreadpublication")
 * @ORM\Entity()
 */
class TbmsgP
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\LogMessages\MsgsP", inversedBy="tabreaders")
     */
    private $idmessage;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idispatch;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Notifications\Notifdispatch")
     * @ORM\JoinColumn(nullable=true)
     */
    private $tabnotifs;

    /**
     *
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $isRead=false;

    /**
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $read_at;

    /**
     *
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $removed=false;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function getReadAt(): ?\DateTimeInterface
    {
        return $this->read_at;
    }

    public function setReadAt(?\DateTimeInterface $read_at): self
    {
        $this->read_at = $read_at;

        return $this;
    }

    public function getRemoved(): ?bool
    {
        return $this->removed;
    }

    public function setRemoved(bool $removed): self
    {
        $this->removed = $removed;

        return $this;
    }

    public function getIdmessage(): ?MsgsP
    {
        return $this->idmessage;
    }

    public function setIdmessage(?MsgsP $idmessage): self
    {
        $this->idmessage = $idmessage;

        return $this;
    }

    public function getTabnotifs(): ?Notifdispatch
    {
        return $this->tabnotifs;
    }

    public function setTabnotifs(?Notifdispatch $tabnotifs): self
    {
        $this->tabnotifs = $tabnotifs;

        return $this;
    }

    public function getIdispatch(): ?int
    {
        return $this->idispatch;
    }

    public function setIdispatch(?int $idispatch): self
    {
        $this->idispatch = $idispatch;

        return $this;
    }

}