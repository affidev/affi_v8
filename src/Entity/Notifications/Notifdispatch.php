<?php

namespace App\Entity\Notifications;


use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\LogMessages\TbmsgD;
use App\Entity\LogMessages\TbmsgP;
use App\Entity\LogMessages\Tbmsgs;
use DateTime;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_notifsdispatch")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\NotifdispatchRepository")
 */
class Notifdispatch
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\App\Entity\DispatchSpace\DispatchSpaceWeb", inversedBy="tbnotifs")
     */
    private $dispatch;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $subject;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $classmodule;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idmodule;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $create_at;


    public function __construct()
    {
        $this->create_at=new DateTime();
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

    public function getClassmodule(): ?string
    {
        return $this->classmodule;
    }

    public function setClassmodule(string $classmodule): self
    {
        $this->classmodule = $classmodule;

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

    public function getDispatch(): ?DispatchSpaceWeb
    {
        return $this->dispatch;
    }

    public function setDispatch(?DispatchSpaceWeb $dispatch): self
    {
        $this->dispatch = $dispatch;

        return $this;
    }

    public function getIdmodule(): ?int
    {
        return $this->idmodule;
    }

    public function setIdmodule(int $idmodule): self
    {
        $this->idmodule = $idmodule;

        return $this;
    }



}