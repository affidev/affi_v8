<?php


namespace App\Entity\Bulles;

use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\UserMap\Taguery;
use Carbon\Carbon;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_bulles", indexes={@ORM\Index(columns={"modulebubble"})})
 * @ORM\Entity(repositoryClass="App\Repository\Entity\BulleRepository")
 */
class Bulle
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $deleted=false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $quality;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbrvieuw =0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbreponse;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $lasttrip_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $spacevisiting;

    /**
     * @ORM\ManyToMany(targetEntity="\App\Entity\DispatchSpace\DispatchSpaceWeb", mappedBy="bulles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Dispatchp;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $modulebubble;

    /**
     *
     * @ORM\Column(type="integer", nullable=false))
     */
    private $idmodule;

    /**
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
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateUpdate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Bulles\Bullette", mappedBy="bulle")
     * @ORM\JoinColumn(nullable=false)
     */
    private $bullettes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\UserMap\Taguery", inversedBy="bulles")
     * @ORM\JoinColumn(nullable=true)
     */
    private $tagueries;

    public function __construct()
    {
        $this->create_at=new DateTime();
        $this->lasttrip_at=new DateTime();
        $this->bullettes = new ArrayCollection();
        $this->tagueries = new ArrayCollection();
        $this->Dispatchp = new ArrayCollection();
        $this->nbrvieuw =1;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): self
    {
        $this->valid = $valid;

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

    public function getQuality(): ?int
    {
        return $this->quality;
    }

    public function setQuality(?int $quality): self
    {
        $this->quality = $quality;

        return $this;
    }

    public function getNbrvieuw(): ?int
    {
        return $this->nbrvieuw;
    }

    public function setNbrvieuw(?int $nbrvieuw): self
    {
        $this->nbrvieuw = $nbrvieuw;

        return $this;
    }

    public function getNbreponse(): ?int
    {
        return $this->nbreponse;
    }

    public function setNbreponse(?int $nbreponse): self
    {
        $this->nbreponse = $nbreponse;

        return $this;
    }

    public function getLasttripAt(): ?\DateTimeInterface
    {
        return $this->lasttrip_at;
    }

    public function setLasttripAt(\DateTimeInterface $lasttrip_at): self
    {
        $this->lasttrip_at = $lasttrip_at;

        return $this;
    }

    public function getSpacevisiting(): ?string
    {
        return $this->spacevisiting;
    }

    public function setSpacevisiting(?string $spacevisiting): self
    {
        $this->spacevisiting = $spacevisiting;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->create_at;
    }

    public function getCreatedAtAgo(): string{
        return Carbon::instance($this->getCreateAt())->diffForHumans();
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

    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->dateUpdate;
    }

    public function setDateUpdate(?\DateTimeInterface $dateUpdate): self
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    public function getModulebubble(): ?string
    {
        return $this->modulebubble;
    }

    public function setModulebubble(string $modulebubble): self
    {
        $this->modulebubble = $modulebubble;

        return $this;
    }

    public function getIdmodule(): ?int
    {
        return $this->idmodule;
    }

    public function setIdmodule(int $idmodule): self
    {
        $this->idmodule = $idmodule;

        return $this;
    }


    /**
     * @return Collection|Bullette[]
     */
    public function getBullettes(): Collection
    {
        return $this->bullettes;
    }

    public function addBullette(Bullette $bullette): self
    {
        if (!$this->bullettes->contains($bullette)) {
            $this->bullettes[] = $bullette;
            $bullette->setBulle($this);
        }

        return $this;
    }

    public function removeBullette(Bullette $bullette): self
    {
        if ($this->bullettes->removeElement($bullette)) {
            // set the owning side to null (unless already changed)
            if ($bullette->getBulle() === $this) {
                $bullette->setBulle(null);
            }
        }

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
     * @return Collection|DispatchSpaceWeb[]
     */
    public function getDispatchp(): Collection
    {
        return $this->Dispatchp;
    }

    public function addDispatchp(DispatchSpaceWeb $dispatchp): self
    {
        if (!$this->Dispatchp->contains($dispatchp)) {
            $this->Dispatchp[] = $dispatchp;
            $dispatchp->addBulle($this);
        }

        return $this;
    }

    public function removeDispatchp(DispatchSpaceWeb $dispatchp): self
    {
        if ($this->Dispatchp->removeElement($dispatchp)) {
            $dispatchp->removeBulle($this);
        }

        return $this;
    }
}