<?php

namespace App\Entity\Posts;

use ApiPlatform\Core\Annotation\ApiFilter;
use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\DispatchSpace\Spwsite;
use App\Entity\Media\Media;
use App\Entity\Module\TabpublicationMsgs;
use App\Entity\Sector\Gps;
use App\Entity\UserMap\Taguery;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Table(name="aff_postation", indexes={@ORM\Index(columns={"keymodule"})})
 * @ORM\Entity(repositoryClass="App\Repository\Entity\PostRepository")
 * @UniqueEntity("slug")
 * @ApiResource(
 *     collectionOperations={
 *          "get"={"path"="/ressources/affi/post/search/allfind/"}},
 *     itemOperations={
 *          "get"={"path"="/ressources/affi/post/search/find/{id}"}},
 *     normalizationContext={"groups"={"postation_post:read","postation_post_full:read"}}
 *)
 * @ApiFilter(BooleanFilter::class, properties={"deleted"})
 * @ApiFilter(OrderFilter::class, properties={"createAt": "DESC"})
 * @ApiFilter(BooleanFilter::class, properties={"publied"})
 * @ApiFilter(SearchFilter::class, properties={"keymodule": "exact"})
 */

class Post
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     * @Groups({"postation_post:read"})
     */
    private $id;

    /**
     * @Groups({"postation_post:read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $keymodule;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Module\TabpublicationMsgs", mappedBy="post", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $tbmessages;

    /**
     * @Groups({"postation_post:read"})
     * @ORM\Column(type="string", length=190, nullable=false)
     */
    private $titre;

    /**
     *
     * @Groups({"postation_post:read"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $subject;

    /**
     * @Groups({"postation_post:read"})
     * @ORM\ManyToMany(targetEntity="App\Entity\UserMap\Taguery", inversedBy="postations")
     * @ORM\JoinColumn(nullable=true)
     */
    private $tagueries;

    /**
     *
     * @Groups({"postation_post:read"})
     * @ORM\ManyToOne(targetEntity="App\Entity\DispatchSpace\DispatchSpaceWeb", inversedBy="postations")
     * @ORM\JoinColumn(nullable=true)
     */
    private $author;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $allmember = true;

    /**
     * @Groups({"postation_post:read"})
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $private = false;

    /**
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\DispatchSpace\Spwsite", inversedBy="postmember")
     * @ORM\JoinColumn(nullable=true)
     */
    private $members;

    /**
     * @Groups({"postation_post:read"})
     * @ORM\OneToOne(targetEntity="App\Entity\Media\Media", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true))
     */
    private $media;

    /**
     * @Groups({"postation_post:read"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Sector\Gps")
     * @ORM\JoinColumn(nullable=true)
     */
    private $localisation;

    /**
     * @Groups({"postation_post:read"})
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $modifAt;

    /**
     * @Groups({"postation_post_full:read"})
     * @ORM\OneToOne(targetEntity="App\Entity\Posts\Article", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $htmlcontent;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $active = true;

    /**
     * @Groups({"postation_post:read"})
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $publied = false;

    /**
     * @Groups({"postation_post:read"})
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $deleted = false;

    /**
     * @Groups({"postation_post:read"})
     * @ORM\Column(type="string", length=190)
     */
    private $slug;

    public function __construct()
    {
        $this->createAt=new DateTimeImmutable();
        $this->tagueries = new ArrayCollection();
        $this->members = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function  __toString()
    {
        return $this->titre;
    }

    public function postSlug(SluggerInterface $slugger)
    {
        if (!$this->slug || '-' === $this->slug) {
            $this->slug = (string) $slugger->slug((string) $this)->lower();
        }
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTagueries(): Collection
    {
        return $this->tagueries;
    }

    public function addTaguery(Taguery $taguery): self
    {
        if (!$this->tagueries->contains($taguery)) {
            $this->tagueries[] = $taguery;
        }

        return $this;
    }

    public function removeTaguery(Taguery $taguery): self
    {
        if ($this->tagueries->contains($taguery)) {
            $this->tagueries->removeElement($taguery);
        }

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): self
    {
        $this->media = $media;

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

    public function getModifAt(): ?\DateTimeInterface
    {
        return $this->modifAt;
    }

    public function setModifAt(\DateTimeInterface $modifAt): self
    {
        $this->modifAt = $modifAt;

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

    public function setCreateAt(\DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getHtmlcontent(): ?Article
    {
        return $this->htmlcontent;
    }

    public function setHtmlcontent(?Article $htmlcontent): self
    {
        $this->htmlcontent = $htmlcontent;

        return $this;
    }
    public function setPublied(bool $publied): Post
    {
        $this->publied=$publied;
        return $this;
    }

    public function getPublied(): ?bool
    {
        return $this->publied;
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

    public function getLocalisation(): ?Gps
    {
        return $this->localisation;
    }

    public function setLocalisation(?Gps $localisation): self
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getTbmessages(): ?TabpublicationMsgs
    {
        return $this->tbmessages;
    }

    public function setTbmessages(?TabpublicationMsgs $tbmessages): self
    {
        $this->tbmessages = $tbmessages;

        return $this;
    }

    public function isAllmember(): ?bool
    {
        return $this->allmember;
    }

    public function setAllmember(bool $allmember): self
    {
        $this->allmember = $allmember;

        return $this;
    }

    public function getPrivate(): ?bool
    {
        return $this->private;
    }

    public function setPrivate(bool $private): self
    {
        $this->private = $private;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(Spwsite $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
        }

        return $this;
    }

    public function removeMember(Spwsite $member): self
    {
        $this->members->removeElement($member);

        return $this;
    }

    public function getAllmember(): ?bool
    {
        return $this->allmember;
    }

}