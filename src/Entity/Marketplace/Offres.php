<?php


namespace App\Entity\Marketplace;


use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use App\Entity\Agenda\Appointments;
use App\Entity\Customer\Transactions;
use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\Media\Media;
use App\Entity\Module\TabpublicationMsgs;
use App\Entity\Sector\Gps;;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\String\Slugger\SluggerInterface;



/**
 * @ORM\Table(name="aff_offres", indexes={@ORM\Index(columns={"keymodule"})})
 * @ORM\Entity(repositoryClass="App\Repository\Entity\OffresRepository")
 * @UniqueEntity("slug")
 * @ApiResource(
 *     collectionOperations={
 *          "get"={"path"="/ressources/affi/offre/allfind/"}},
 *     itemOperations={
 *          "get"={"path"="/ressources/affi/offre/find/{id}"}},
 *     normalizationContext={"groups"={"offres_post:read"}}
 *)
 * @ApiFilter(BooleanFilter::class, properties={"deleted"})
 * @ApiFilter(OrderFilter::class, properties={"createAt": "DESC"})
 * @ApiFilter(SearchFilter::class, properties={"keymodule": "exact"})
 * @ApiFilter(DateFilter::class, properties={"idparutions.days"})
 */
class Offres
{
    /**
     * @Groups({"offres_post:read"})
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Module\TabpublicationMsgs", mappedBy="offre", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $tbmessages;

    /**
     * @Groups({"offres_post:read", "parutions:read", "simplebuble"})
     * @ORM\OneToOne(targetEntity="App\Entity\Marketplace\Categories")
     * @ORM\JoinColumn(nullable=true)
     */
    private $catagory;

    /**
     * @Groups({"offres_post:read", "parutions:read", "simplebuble"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $keymodule;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $tabunique;

    /**
     * @Groups({"offres_post:read","appointoffre:read"})
     * @ORM\OneToOne(targetEntity="App\Entity\Agenda\Appointments", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $parution;

    /**
     * @Groups({"offres_post:read","parutions:read", "simplebuble"})
     * @Assert\Length(
     * min=10,
     * max=190,
     * minMessage="le titre doit faire au moins {{ limit }} caractères",
     * maxMessage="le titre doit faire au maximum {{ limit }} caractères")
     * @ORM\Column(type="string", length=190, nullable=false)
     */
    private $titre;

    /**
     * @Groups({"offres_post:read", "parutions:read", "simplebuble"})
     * min=10,
     * max=250,
     * minMessage="le titre doit faire au moins {{ limit }} caractères",
     * maxMessage="le titre doit faire au maximum {{ limit }} caractères")
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $descriptif;

    /**
     * @Groups({"offres_post:read", "parutions:read", "simplebuble"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Marketplace\Noticeproducts", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $product;

    /**
     *
     * @Groups({"offres_post:read", "parutions:read", "simplebuble"})
     * @ORM\ManyToOne(targetEntity="App\Entity\DispatchSpace\DispatchSpaceWeb", inversedBy="postations")
     * @ORM\JoinColumn(nullable=true)
     */
    private $author;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $isotherdest = false;

    /**
     * @var string
     * @Groups({"offres_post:read", "parutions:read", "simplebuble"})
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $destinataire;

    /**
     * @Groups({"offres_post:read", "parutions:read", "simplebuble"})
     * @ORM\OneToOne(targetEntity="App\Entity\Media\Media", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true))
     */
    private $media;

    /**
     * @Groups({"offres_post:read", "parutions:read"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Sector\Gps", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $localisation;

    /**
     * @Groups({"offres_post:read", "parutions:read", "simplebuble"})
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    /**
     * @Groups({"offres_post:read"})
     * @ORM\Column(type="datetime")
     */
    private $endAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $modifAt;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $active = true;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $deleted = false;

    /**
     * @Groups({"offres_post:read"})
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $publied = true;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $promo = false;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $bulled = false;


    /**
     * @Groups({"offres_post:read","parutions:read"})
     * @ORM\Column(type="string", length=190, unique=true)
     */
    private $slug;

    /**
     * @Groups({"offres_post:read", "parutions:read"})
     * @ORM\Column(type="string", length=250)
     */
    private $adverse;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Customer\Transactions", mappedBy="offre")
     * @ORM\JoinColumn(nullable=true)
     */
    private $transactions;

    public function __construct()
    {
        $this->createAt=new DateTimeImmutable();
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function  __toString()
    {
        return $this->titre;
    }

    public function offreSlug(SluggerInterface $slugger)
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

    public function getDescriptif(): ?string
    {
        return $this->descriptif;
    }

    public function setDescriptif(?string $descriptif): self
    {
        $this->descriptif = $descriptif;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getModifAt(): ?\DateTimeInterface
    {
        return $this->modifAt;
    }

    public function setModifAt(?\DateTimeInterface $modifAt): self
    {
        $this->modifAt = $modifAt;

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

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }
    public function setPublied(bool $publied): Offres
    {
        $this->publied=$publied;
        return $this;
    }

    public function getPublied(): ?bool
    {
        return $this->publied;
    }

    public function setBulled(bool $bulled): self
    {
        $this->bulled = $bulled;
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

    public function getAdverse(): ?string
    {
        return $this->adverse;
    }

    public function setAdverse(string $adverse): self
    {
        $this->adverse = $adverse;

        return $this;
    }

    public function getProduct(): ?Noticeproducts
    {
        return $this->product;
    }

    public function setProduct(?Noticeproducts $product): self
    {
        $this->product = $product;

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

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): self
    {
        $this->media = $media;

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

    public function isbulled(): ?bool
    {
        return $this->bulled;
    }

    public function getTabunique(): ?string
    {
        return $this->tabunique;
    }

    public function setTabunique(string $tabunique): self
    {
        $this->tabunique = $tabunique;

        return $this;
    }

    public function getBulled(): ?bool
    {
        return $this->bulled;
    }

    public function getParution(): ?Appointments
    {
        return $this->parution;
    }

    public function setParution(Appointments $parution): self
    {
        $this->parution = $parution;

        return $this;
    }

    public function isPromo(): bool
    {
        return $this->promo;
    }

    public function setPromo(bool $promo): Offres
    {
        $this->promo = $promo;
        return $this;
    }

    public function getPromo(): ?bool
    {
        return $this->promo;
    }

    /**
     * @return Collection
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transactions $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setOffre($this);
        }

        return $this;
    }

    public function removeTransaction(Transactions $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getOffre() === $this) {
                $transaction->setOffre(null);
            }
        }

        return $this;
    }

    public function getIsotherdest(): ?bool
    {
        return $this->isotherdest;
    }

    public function setIsotherdest(bool $isotherdest): self
    {
        $this->isotherdest = $isotherdest;

        return $this;
    }

    public function getDestinataire(): ?string
    {
        return $this->destinataire;
    }

    public function setDestinataire(?string $destinataire): self
    {
        $this->destinataire = $destinataire;

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

    public function getTbmessages(): ?TabpublicationMsgs
    {
        return $this->tbmessages;
    }

    public function setTbmessages(?TabpublicationMsgs $tbmessages): self
    {
        $this->tbmessages = $tbmessages;

        return $this;
    }

    public function getCatagory(): ?Categories
    {
        return $this->catagory;
    }

    public function setCatagory(?Categories $catagory): self
    {
        $this->catagory = $catagory;

        return $this;
    }

}