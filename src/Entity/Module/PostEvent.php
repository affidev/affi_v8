<?php


namespace App\Entity\Module;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Agenda\Appointments;
use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\Media\Media;
use App\Entity\Sector\Sectors;
use App\Entity\UserMap\Taguery;
use App\Entity\Websites\Website;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Table(name="aff_postevent", indexes={@ORM\Index(columns={"keymodule"})})
 * @ORM\Entity(repositoryClass="App\Repository\Entity\PostEventRepository")
 *  @ApiResource(
 *     collectionOperations={
 *          "get"={"path"="/ressources/affi/event/search/allfind/"}},
 *     itemOperations={
 *          "get"={"path"="/ressources/affi/event/search/find/{id}"}},
 *     normalizationContext={"groups"={"event_post:read"}}
 *)
 * @ApiFilter(BooleanFilter::class, properties={"deleted"})
 * @ApiFilter(OrderFilter::class, properties={"createAt": "DESC"})
 * @ApiFilter(BooleanFilter::class, properties={"publied"})
 * @ApiFilter(SearchFilter::class, properties={"keymodule": "exact"})
 */
class PostEvent
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     * @Groups({"edit_event"})
     */
    private $id;

    /**
     * @Groups({"event_post:read"})
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $keymodule;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Websites\Website", mappedBy="eventpartner" )
     * @ORM\JoinColumn(nullable=true)
     */
    private $partners;

    /**
     * @ORM\OneToOne (targetEntity="App\Entity\Sector\Sectors" )
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"event_post:read","edit_event"})
     */
    private $sector;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DispatchSpace\DispatchSpaceWeb", mappedBy="eventpartner")
     * @ORM\JoinColumn(nullable=true)
     */
    private $associate;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\DispatchSpace\DispatchSpaceWeb")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Agenda\Appointments", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"event_post:read","edit_event"})
     */
    private $appointment;

    /**
     * @ORM\Column(type="string", length=190, nullable=false)
     * @Groups({"event_post:read","edit_event"})
     */
    private $titre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"event_post:read","edit_event"})
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\UserMap\Taguery", inversedBy="postevents")
     * @ORM\JoinColumn(nullable=true)
     */
    private $tagueries;

    /**
     * @ORM\Column(type="text",  nullable=true)
     * @Groups({"event_post:read"})
     */
    private $content;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Media\Media", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true))
     * @Groups({"event_post:read","edit_event"})
     */
    private $media;

    /**
     * @Groups({"event_post:read"})
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $publied = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"event_post:read"})
     */
    private $datemaj_at;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"event_post:read"})
     */
    private $create_at;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     * @Groups({"event_post:read"})
     */
    private $deleted = false;


    public function __construct()
    {
        $this->tagueries = new ArrayCollection();
        $this->create_at=new \DateTimeImmutable();
        $this->associate = new ArrayCollection();
        $this->partners = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->titre;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKeymodule(): ?string
    {
        return $this->keymodule;
    }

    public function setKeymodule(string $keymodule): self
    {
        $this->keymodule = $keymodule;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDatemajAt(): ?\DateTimeInterface
    {
        return $this->datemaj_at;
    }

    public function setDatemajAt(?\DateTimeInterface $datemaj_at): self
    {
        $this->datemaj_at = $datemaj_at;

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

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getAppointment(): ?Appointments
    {
        return $this->appointment;
    }

    public function setAppointment(Appointments $appointment): self
    {
        $this->appointment = $appointment;

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
        $this->tagueries->removeElement($taguery);

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


    /**
     * @return Collection
     */
    public function getAssociate(): Collection
    {
        return $this->associate;
    }

    public function addAssociate(DispatchSpaceWeb $associate): self
    {
        if (!$this->associate->contains($associate)) {
            $this->associate[] = $associate;
            $associate->setEventpartner($this);
        }

        return $this;
    }

    public function removeAssociate(DispatchSpaceWeb $associate): self
    {
        if ($this->associate->removeElement($associate)) {
            // set the owning side to null (unless already changed)
            if ($associate->getEventpartner() === $this) {
                $associate->setEventpartner(null);
            }
        }

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

    /**
     * @return Collection|Website[]
     */
    public function getPartners(): Collection
    {
        return $this->partners;
    }

    public function addPartner(Website $partner): self
    {
        if (!$this->partners->contains($partner)) {
            $this->partners[] = $partner;
            $partner->addEventpartner($this);
        }

        return $this;
    }

    public function removePartner(Website $partner): self
    {
        if ($this->partners->removeElement($partner)) {
            $partner->removeEventpartner($this);
        }

        return $this;
    }

    public function getSector(): ?Sectors
    {
        return $this->sector;
    }

    public function setSector(?Sectors $sector): self
    {
        $this->sector = $sector;

        return $this;
    }

    public function getPublied(): ?bool
    {
        return $this->publied;
    }

    public function setPublied(bool $publied): self
    {
        $this->publied = $publied;

        return $this;
    }

}