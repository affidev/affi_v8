<?php


namespace App\Entity\LogMessages;

use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\Users\Contacts;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="aff_msgspublication")
 * @ORM\Entity()
 */
class MsgsP
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\App\Entity\DispatchSpace\DispatchSpaceWeb")
     * @ORM\JoinColumn(nullable=true)
     */
    private $authordispatch;

    /**
     * @ORM\ManyToOne(targetEntity="\App\Entity\Users\Contacts")
     * @ORM\JoinColumn(nullable=true)
     */
    private $authorcontact;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\LogMessages\PublicationConvers", inversedBy="msgs")
     * @ORM\JoinColumn(nullable=true)
     */
    private $publicationmsg;

    /**
     * @var string
     *
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
     * @ORM\OneToMany(targetEntity="App\Entity\LogMessages\TbmsgP", mappedBy="idmessage", cascade={"persist", "remove"})
     */
    private $tabreaders;

    /**
     *
     * @ORM\OneToOne(targetEntity="App\Entity\LogMessages\Loginner", mappedBy="msg", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $msglog;

    public function __construct()
    {
        $this->create_at = new DateTime();
        $this->tabreaders = new ArrayCollection();
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

    public function getAuthordispatch(): ?DispatchSpaceWeb
    {
        return $this->authordispatch;
    }

    public function setAuthordispatch(?DispatchSpaceWeb $authordispatch): self
    {
        $this->authordispatch = $authordispatch;

        return $this;
    }

    public function getAuthorcontact(): ?Contacts
    {
        return $this->authorcontact;
    }

    public function setAuthorcontact(?Contacts $authorcontact): self
    {
        $this->authorcontact = $authorcontact;

        return $this;
    }

    public function getPublicationmsg(): ?PublicationConvers
    {
        return $this->publicationmsg;
    }

    public function setPublicationmsg(?PublicationConvers $publicationmsg): self
    {
        $this->publicationmsg = $publicationmsg;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTabreaders(): Collection
    {
        return $this->tabreaders;
    }

    public function addTabreader(TbmsgP $tabreader): self
    {
        if (!$this->tabreaders->contains($tabreader)) {
            $this->tabreaders[] = $tabreader;
            $tabreader->setIdmessage($this);
        }

        return $this;
    }

    public function removeTabreader(TbmsgP $tabreader): self
    {
        if ($this->tabreaders->removeElement($tabreader)) {
            // set the owning side to null (unless already changed)
            if ($tabreader->getIdmessage() === $this) {
                $tabreader->setIdmessage(null);
            }
        }

        return $this;
    }

    public function getMsglog(): ?Loginner
    {
        return $this->msglog;
    }

    public function setMsglog(?Loginner $msglog): self
    {
        $this->msglog = $msglog;

        return $this;
    }

}