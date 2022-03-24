<?php


namespace App\Entity\Job;

use ApiPlatform\Core\Annotation\ApiFilter;
use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\Media\Media;
use App\Entity\Module\Recrutation;
use App\Entity\Posts\Article;
use App\Entity\Sector\Gps;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="aff_offer")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\OfferRepository")
 * @UniqueEntity("slug")
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"offer:read"}}
 *)
 * @ApiFilter(BooleanFilter::class, properties={"deleted"})
 * @ApiFilter(OrderFilter::class, properties={"createAt": "DESC"})
 */
class Offer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     * @Groups({"offer:read","module_post:read", "website_post:read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Module\Recrutation", inversedBy="offers")
     * @ORM\JoinColumn(nullable=true)
     */
    private $recrutation;

    /**
     * @Groups({"offer:read","module_post:read", "website_post:read"})
     * @ORM\Column(type="string", length=190, nullable=false)
     */
    private $titre;

    /**
     * @Groups({"offer:read","module_post:read", "website_post:read"})
     * @ORM\Column(type="string", length=190, nullable=false)
     */
    private $reference;

    /**
     * @Groups({"offer:read","module_post:read", "website_post:read"})
     * @ORM\Column(type="string", length=190, nullable=false)
     */
    private $contrat;

    /**
     * @Groups({"offer:read","module_post:read", "website_post:read"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $profil;

    /**
     * @Groups({"postation_post:read","module_post:read", "website_post:read"})
     * @ORM\OneToOne(targetEntity="App\Entity\Media\Media", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true))
     */
    private $media;

    /**
     *
     * @Groups({"offer:read","module_post:read", "website_post:read"})
     * @ORM\ManyToOne(targetEntity="App\Entity\DispatchSpace\DispatchSpaceWeb")
     * @ORM\JoinColumn(nullable=true)
     */
    private $author;
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Sector\Gps", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $localisation;

    /**
     * @Groups({"offer:read","module_post:read", "website_post:read"})
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $modifAt;

    /**
     * @Groups({"offer:read","module_post:read", "website_post:read"})
     * @ORM\OneToOne(targetEntity="App\Entity\Posts\Article", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $htmlcontent;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $active = true;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $deleted = false;

    /**
     * @Groups({"offer:read","module_post:read", "website_post:read"})
     * @ORM\Column(type="string", length=190, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Job\TransactionsOffer", mappedBy="offer")
     * @ORM\JoinColumn(nullable=true)
     */
    private $transactions;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $keymodule;

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

    public function offerSlug(SluggerInterface $slugger)
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

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getContrat(): ?string
    {
        return $this->contrat;
    }

    public function setContrat(string $contrat): self
    {
        $this->contrat = $contrat;

        return $this;
    }

    public function getProfil(): ?string
    {
        return $this->profil;
    }

    public function setProfil(?string $profil): self
    {
        $this->profil = $profil;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getAuthor(): ?DispatchSpaceWeb
    {
        return $this->author;
    }

    public function setAuthor(?DispatchSpaceWeb $author): self
    {
        $this->author = $author;

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

    public function getHtmlcontent(): ?Article
    {
        return $this->htmlcontent;
    }

    public function setHtmlcontent(?Article $htmlcontent): self
    {
        $this->htmlcontent = $htmlcontent;

        return $this;
    }

    public function getOffertation(): ?Recrutation
    {
        return $this->offertation;
    }

    public function setOffertation(?Recrutation $offertation): self
    {
        $this->offertation = $offertation;

        return $this;
    }

    public function getRecrutation(): ?Recrutation
    {
        return $this->recrutation;
    }

    public function setRecrutation(?Recrutation $recrutation): self
    {
        $this->recrutation = $recrutation;

        return $this;
    }

    /**
     * @return Collection|TransactionsOffer[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(TransactionsOffer $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setOffer($this);
        }

        return $this;
    }

    public function removeTransaction(TransactionsOffer $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getOffer() === $this) {
                $transaction->setOffer(null);
            }
        }

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

}