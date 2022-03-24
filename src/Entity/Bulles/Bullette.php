<?php


namespace App\Entity\Bulles;

use App\Entity\DispatchSpace\DispatchSpaceWeb;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="aff_bullette")
 * @ORM\Entity
 */
class Bullette
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Bulles\Bulle", inversedBy="bullettes")
     */
    private $bulle;

    /**
     * @ORM\ManyToOne(targetEntity="\App\Entity\DispatchSpace\DispatchSpaceWeb")
     * @ORM\JoinColumn(nullable=false)
     */
    private $spacewebanswser;

    /**
     * @var string
     *
     * @Groups({"buble"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $contentHtml;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(min=5)
     */
    private $bodyTxt;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $create_at;

    /**
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expire_at;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ownerOfgroup;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $warning;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $deleted;

    public function __construct()
    {
        $this->create_at=new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContentHtml(): ?string
    {
        return $this->contentHtml;
    }

    public function setContentHtml(?string $contentHtml): self
    {
        $this->contentHtml = $contentHtml;

        return $this;
    }

    public function getBodyTxt(): ?string
    {
        return $this->bodyTxt;
    }

    public function setBodyTxt(?string $bodyTxt): self
    {
        $this->bodyTxt = $bodyTxt;

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

    public function getExpireAt(): ?\DateTimeInterface
    {
        return $this->expire_at;
    }

    public function setExpireAt(?\DateTimeInterface $expire_at): self
    {
        $this->expire_at = $expire_at;

        return $this;
    }

    public function getWarning(): ?bool
    {
        return $this->warning;
    }

    public function setWarning(?bool $warning): self
    {
        $this->warning = $warning;

        return $this;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(?bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getBulle(): ?Bulle
    {
        return $this->bulle;
    }

    public function setBulle(?Bulle $bulle): self
    {
        $this->bulle = $bulle;

        return $this;
    }

    public function getOwnerOfgroup(): ?bool
    {
        return $this->ownerOfgroup;
    }

    public function setOwnerOfgroup(?bool $ownerOfgroup): self
    {
        $this->ownerOfgroup = $ownerOfgroup;

        return $this;
    }

    public function getSpacewebanswser(): ?DispatchSpaceWeb
    {
        return $this->spacewebanswser;
    }

    public function setSpacewebanswser(?DispatchSpaceWeb $spacewebanswser): self
    {
        $this->spacewebanswser = $spacewebanswser;

        return $this;
    }
}