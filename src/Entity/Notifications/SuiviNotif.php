<?php

namespace App\Entity\Notifications;

use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\Users\Contacts;
use App\Entity\Websites\Website;
use Doctrine\Common\Collections\ArrayCollection;
use DateTime;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_suivinotif")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\SuiviNotifRepository")
 */
class SuiviNotif
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
    private $isread = false;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $ismember = false;

    /**
     * @ORM\ManyToOne(targetEntity="\App\Entity\DispatchSpace\DispatchSpaceWeb")
     * @ORM\JoinColumn(nullable=true)
     */
    private $member;

    /**
     * @ORM\ManyToOne(targetEntity="\App\Entity\Websites\Website")
     * @ORM\JoinColumn(nullable=true)
     */
    private $website;

    /**
     * @ORM\ManyToOne(targetEntity="\App\Entity\DispatchSpace\DispatchSpaceWeb")
     * @ORM\JoinColumn(nullable=true)
     */
    private $dispatch;

    /**
     * @ORM\ManyToOne(targetEntity="\App\Entity\Users\Contacts")
     * @ORM\JoinColumn(nullable=true)
     */
    private $contact;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $subject;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mediasource;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $classmodule;

    /**
     *  @ORM\Column(type="integer", nullable=true)
     */
    private $classmoduleid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $keymodule;

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

    public function getIsread(): ?bool
    {
        return $this->isread;
    }

    public function setIsread(bool $isread): self
    {
        $this->isread = $isread;

        return $this;
    }

    public function getIsmember(): ?bool
    {
        return $this->ismember;
    }

    public function setIsmember(bool $ismember): self
    {
        $this->ismember = $ismember;

        return $this;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getMediasource(): ?string
    {
        return $this->mediasource;
    }

    public function setMediasource(?string $mediasource): self
    {
        $this->mediasource = $mediasource;

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

    public function getClassmoduleid(): ?int
    {
        return $this->classmoduleid;
    }

    public function setClassmoduleid(?int $classmoduleid): self
    {
        $this->classmoduleid = $classmoduleid;

        return $this;
    }

    public function getKeymodule(): ?string
    {
        return $this->keymodule;
    }

    public function setKeymodule(?string $keymodule): self
    {
        $this->keymodule = $keymodule;

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

    public function getMember(): ?DispatchSpaceWeb
    {
        return $this->member;
    }

    public function setMember(?DispatchSpaceWeb $member): self
    {
        $this->member = $member;

        return $this;
    }

    public function getWebsite(): ?Website
    {
        return $this->website;
    }

    public function setWebsite(?Website $website): self
    {
        $this->website = $website;

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

    public function getContact(): ?Contacts
    {
        return $this->contact;
    }

    public function setContact(?Contacts $contact): self
    {
        $this->contact = $contact;

        return $this;
    }



}