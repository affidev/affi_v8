<?php


namespace App\Entity\Comments;

use App\Entity\DispatchSpace\DispatchSpaceWeb;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="aff_msgscommentnotice")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\MsgsCommentNoticeRepository")
 */
class MsgsCommentNotice
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
    private $author;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Comments\CommentNotice", inversedBy="msgs")
     * @ORM\JoinColumn(nullable=true)
     */
    private $comment;

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


    public function __construct()
    {
        $this->create_at = new DateTime();
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

    public function getAuthor(): ?DispatchSpaceWeb
    {
        return $this->author;
    }

    public function setAuthor(?DispatchSpaceWeb $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getComment(): ?CommentNotice
    {
        return $this->comment;
    }

    public function setComment(?CommentNotice $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

}