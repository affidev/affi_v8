<?php


namespace App\Entity\DispatchSpace;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Bulles\Bulle;
use App\Entity\Customer\Customers;
use App\Entity\Customer\Avantages;
use App\Entity\Customer\Transactions;
use App\Entity\HyperCom\TagAnalytic;
use App\Entity\Module\PostEvent;
use App\Entity\Notifications\Notifdispatch;
use App\Entity\Posts\Post;
use App\Entity\Sector\Gps;
use App\Entity\Sector\Sectors;
use App\Entity\UserMap\Taguery;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\String\Slugger\SluggerInterface;


/**
 * @ORM\Table(name="aff_dispatch")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\DispatchSpaceWebRepository")
 * @UniqueEntity("slug")
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"customer_adress"}}
 *     )
 */
class DispatchSpaceWeb
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Length(
     * min=3,
     * max=70,
     * minMessage="le titre doit faire au moins {{ limit }} caractères",
     * maxMessage="le titre doit faire au maximum {{ limit }} caractères")
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=70)
     * @Groups({"dispatch_post:read","postation_post:read","module_post:read", "website_post:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="json")
     */
    private $permission = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DispatchSpace\Spwsite", mappedBy="disptachwebsite", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $spwsite;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Posts\Post", mappedBy="author")
     * @ORM\JoinColumn(nullable=true)
     */
    private $postations;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Module\PostEvent", inversedBy="associate")
     * @ORM\JoinColumn(nullable=true)
     */
    private $eventpartner;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Customer\Transactions", mappedBy="client")
     * @ORM\JoinColumn(nullable=true)
     */
    private $transactions;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Customer\Customers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Customer\Avantages", mappedBy="dispatch")
     * @ORM\JoinColumn(nullable=true)
     */
    private $avantages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DispatchSpace\Tballmessage", mappedBy="dispatch")
     * @ORM\JoinColumn(nullable=true)
     */
    private $allmessages;

    /**
     * @Groups({"customer_adress"})
     * @ORM\OneToOne(targetEntity="App\Entity\Sector\Sectors", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $sector;

    /**
     * @ORM\Column(type="string", length=190, unique=true)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sector\Gps")
     * @ORM\JoinColumn(nullable=true)
     */
    private $locality;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datemaj_at;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $active = true;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\DispatchSpace\DispatchSpaceWeb")
     * @ORM\JoinColumn(nullable=true)
     */
    private $dispatchlinks;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\UserMap\Taguery", inversedBy="dispatch")
     * @ORM\JoinColumn(nullable=true)
     */
    private $tagueries;

    /**
     * @ORM\OneToMany(targetEntity="\App\Entity\Notifications\Notifdispatch", mappedBy="dispatch")
     * @ORM\JoinColumn(nullable=true)
     */
    private $tbnotifs;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Bulles\Bulle", inversedBy="Dispatchp")
     * @ORM\JoinColumn(nullable=true)
     */
    private $bulles;

    /**
     * @ORM\OneToOne(targetEntity="\App\Entity\HyperCom\TagAnalytic", inversedBy="dispatch")
     * @ORM\JoinColumn(nullable=true)
     */
    private $analityc;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $about;

    public function __construct()
    {
        $this->create_at=new DateTime();
        $this->spwsite = new ArrayCollection();
        $this->postations = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->avantages = new ArrayCollection();
        $this->allmessages = new ArrayCollection();
        $this->dispatchlinks = new ArrayCollection();
        $this->tagueries = new ArrayCollection();
        $this->tbnotifs = new ArrayCollection();
        $this->bulles = new ArrayCollection();
    }

    public function  __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function dispatchSpaceWebSlug(SluggerInterface $slugger)
    {
       if (!$this->slug || '-' === $this->slug) {
            $this->slug = (string) $slugger->slug((string) $this)->lower();
       }
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPermission(): ?array
    {
        return $this->permission;
    }

    public function setPermission(array $permission): self
    {
        $this->permission = $permission;

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

    public function getDatemajAt(): ?\DateTimeInterface
    {
        return $this->datemaj_at;
    }

    public function setDatemajAt(?\DateTimeInterface $datemaj_at): self
    {
        $this->datemaj_at = $datemaj_at;

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

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(?string $about): self
    {
        $this->about = $about;

        return $this;
    }

    /**
     * @return Collection|Spwsite[]
     */
    public function getSpwsite(): Collection
    {
        return $this->spwsite;
    }

    public function addSpwsite(Spwsite $spwsite): self
    {
        if (!$this->spwsite->contains($spwsite)) {
            $this->spwsite[] = $spwsite;
            $spwsite->setDisptachwebsite($this);
        }

        return $this;
    }

    public function removeSpwsite(Spwsite $spwsite): self
    {
        if ($this->spwsite->removeElement($spwsite)) {
            // set the owning side to null (unless already changed)
            if ($spwsite->getDisptachwebsite() === $this) {
                $spwsite->setDisptachwebsite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPostations(): Collection
    {
        return $this->postations;
    }

    public function addPostation(Post $postation): self
    {
        if (!$this->postations->contains($postation)) {
            $this->postations[] = $postation;
            $postation->setAuthor($this);
        }

        return $this;
    }

    public function removePostation(Post $postation): self
    {
        if ($this->postations->removeElement($postation)) {
            // set the owning side to null (unless already changed)
            if ($postation->getAuthor() === $this) {
                $postation->setAuthor(null);
            }
        }

        return $this;
    }

    public function getEventpartner(): ?PostEvent
    {
        return $this->eventpartner;
    }

    public function setEventpartner(?PostEvent $eventpartner): self
    {
        $this->eventpartner = $eventpartner;

        return $this;
    }

    /**
     * @return Collection|Transactions[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transactions $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setClient($this);
        }

        return $this;
    }

    public function removeTransaction(Transactions $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getClient() === $this) {
                $transaction->setClient(null);
            }
        }

        return $this;
    }

    public function getCustomer(): ?Customers
    {
        return $this->customer;
    }

    public function setCustomer(Customers $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Collection|Avantages[]
     */
    public function getAvantages(): Collection
    {
        return $this->avantages;
    }

    public function addAvantage(Avantages $avantage): self
    {
        if (!$this->avantages->contains($avantage)) {
            $this->avantages[] = $avantage;
            $avantage->setDispatch($this);
        }

        return $this;
    }

    public function removeAvantage(Avantages $avantage): self
    {
        if ($this->avantages->removeElement($avantage)) {
            // set the owning side to null (unless already changed)
            if ($avantage->getDispatch() === $this) {
                $avantage->setDispatch(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tballmessage[]
     */
    public function getAllmessages(): Collection
    {
        return $this->allmessages;
    }

    public function addAllmessage(Tballmessage $allmessage): self
    {
        if (!$this->allmessages->contains($allmessage)) {
            $this->allmessages[] = $allmessage;
            $allmessage->setDispatch($this);
        }

        return $this;
    }

    public function removeAllmessage(Tballmessage $allmessage): self
    {
        if ($this->allmessages->removeElement($allmessage)) {
            // set the owning side to null (unless already changed)
            if ($allmessage->getDispatch() === $this) {
                $allmessage->setDispatch(null);
            }
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

    public function getLocality(): ?Gps
    {
        return $this->locality;
    }

    public function setLocality(?Gps $locality): self
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * @return Collection|DispatchSpaceWeb[]
     */
    public function getDispatchlinks(): Collection
    {
        return $this->dispatchlinks;
    }

    public function addDispatchlink(DispatchSpaceWeb $dispatchlink): self
    {
        if (!$this->dispatchlinks->contains($dispatchlink)) {
            $this->dispatchlinks[] = $dispatchlink;
        }

        return $this;
    }

    public function removeDispatchlink(DispatchSpaceWeb $dispatchlink): self
    {
        $this->dispatchlinks->removeElement($dispatchlink);

        return $this;
    }

    /**
     * @return Collection|Taguery[]
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

    /**
     * @return Collection|Notifdispatch[]
     */
    public function getTbnotifs(): Collection
    {
        return $this->tbnotifs;
    }

    public function addTbnotif(Notifdispatch $tbnotif): self
    {
        if (!$this->tbnotifs->contains($tbnotif)) {
            $this->tbnotifs[] = $tbnotif;
            $tbnotif->setDispatch($this);
        }

        return $this;
    }

    public function removeTbnotif(Notifdispatch $tbnotif): self
    {
        if ($this->tbnotifs->removeElement($tbnotif)) {
            // set the owning side to null (unless already changed)
            if ($tbnotif->getDispatch() === $this) {
                $tbnotif->setDispatch(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Bulle[]
     */
    public function getBulles(): Collection
    {
        return $this->bulles;
    }

    public function addBulle(Bulle $bulle): self
    {
        if (!$this->bulles->contains($bulle)) {
            $this->bulles[] = $bulle;
        }

        return $this;
    }

    public function removeBulle(Bulle $bulle): self
    {
        $this->bulles->removeElement($bulle);

        return $this;
    }

    public function getAnalityc(): ?TagAnalytic
    {
        return $this->analityc;
    }

    public function setAnalityc(?TagAnalytic $analityc): self
    {
        $this->analityc = $analityc;

        return $this;
    }
}