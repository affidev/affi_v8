<?php

namespace App\Entity\Websites;



use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\Users\Contacts;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_tbsuggest")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\TbsuggestRepository")
 */
class Tbsuggest
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Websites\Website")
     * @ORM\JoinColumn(nullable=false)
     */
    private $prewebsite;

    /**
     * @ORM\OneToOne(targetEntity="\App\Entity\Websites\Website")
     * @ORM\JoinColumn(nullable=false)
     */
    private $invitor;

    /**
     * @ORM\OneToOne(targetEntity="\App\Entity\DispatchSpace\DispatchSpaceWeb")
     * @ORM\JoinColumn(nullable=true)
     */
    private $dispatch;

    /**
     * @ORM\OneToOne(targetEntity="\App\Entity\Users\Contacts")
     * @ORM\JoinColumn(nullable=true)
     */
    private $contact;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_at;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $issuggest=false;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $active = true;

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

    public function getIssuggest(): ?bool
    {
        return $this->issuggest;
    }

    public function setIssuggest(bool $issuggest): self
    {
        $this->issuggest = $issuggest;

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

    public function getPrewebsite(): ?Website
    {
        return $this->prewebsite;
    }

    public function setPrewebsite(Website $prewebsite): self
    {
        $this->prewebsite = $prewebsite;

        return $this;
    }

    public function getInvitor(): ?Website
    {
        return $this->invitor;
    }

    public function setInvitor(Website $invitor): self
    {
        $this->invitor = $invitor;

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