<?php


namespace App\Entity\LogMessages;

use DateTime;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_loginner")
 * @ORM\Entity()
 */
class Loginner
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $ip;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $uri;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $referer;

    /**
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $agent;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $create_at;

    /**
     *
     * @ORM\OneToOne(targetEntity="App\Entity\LogMessages\Msgs", inversedBy="msglog")
     */
    private $msg;

    public function __construct()
    {
        $this->create_at = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(?string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function getReferer(): ?string
    {
        return $this->referer;
    }

    public function setReferer(?string $referer): self
    {
        $this->referer = $referer;

        return $this;
    }

    public function getAgent(): ?string
    {
        return $this->agent;
    }

    public function setAgent(?string $agent): self
    {
        $this->agent = $agent;

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

    public function getMsg(): ?Msgs
    {
        return $this->msg;
    }

    public function setMsg(?Msgs $msg): self
    {
        $this->msg = $msg;

        // set (or unset) the owning side of the relation if necessary
        $newMsglog = null === $msg ? null : $this;
        if ($msg->getMsglog() !== $newMsglog) {
            $msg->setMsglog($newMsglog);
        }

        return $this;
    }

}