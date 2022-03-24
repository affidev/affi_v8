<?php


namespace App\Entity\Comments;


use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\Users\Contacts;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_commentnotice")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\CommentNoticeRepository")
 */
class CommentNotice
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
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $isclient = false;


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
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $create_at;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Comments\MsgsCommentNotice", mappedBy="comment", cascade={"persist", "remove"})
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

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->create_at;
    }

    public function setCreateAt(\DateTimeInterface $create_at): self
    {
        $this->create_at = $create_at;

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
     * @return Collection|MsgsCommentNotice[]
     */
    public function getMsgs(): Collection
    {
        return $this->msgs;
    }

    public function addMsg(MsgsCommentNotice $msg): self
    {
        if (!$this->msgs->contains($msg)) {
            $this->msgs[] = $msg;
            $msg->setComment($this);
        }

        return $this;
    }

    public function removeMsg(MsgsCommentNotice $msg): self
    {
        if ($this->msgs->contains($msg)) {
            $this->msgs->removeElement($msg);
            // set the owning side to null (unless already changed)
            if ($msg->getComment() === $this) {
                $msg->setComment(null);
            }
        }

        return $this;
    }

}