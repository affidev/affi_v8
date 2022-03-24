<?php

namespace App\Entity\UserMap;

use App\Entity\Sector\Gps;
use App\Entity\Websites\Website;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_hits")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\HitsRepository")
 */
class Hits
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Websites\Website", inversedBy="hits")
     * @ORM\JoinColumn(nullable=true)
     */
    private $website;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sector\Gps", inversedBy="hits")
     * @ORM\JoinColumn(nullable=true)
     */
    private $gps;

    /**
     * @ORM\Column(type="integer",  options={"default":0})
     */
    private $publi=0;

    /**
     * @ORM\Column(type="integer",  options={"default":0})
     */
    private $liked=0;

    /**
     * @var datetime $dateinsert
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastdayshow;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserMap\Tagcat", mappedBy="hits")
     * @ORM\JoinColumn(nullable=true)
     */
    private $catag;

    public function __construct()
    {
        $this->catag = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPubli(): ?int
    {
        return $this->publi;
    }

    public function setPubli(int $publi): self
    {
        $this->publi = $publi;

        return $this;
    }

    public function getLiked(): ?int
    {
        return $this->liked;
    }

    public function setLiked(int $liked): self
    {
        $this->liked = $liked;

        return $this;
    }

    public function getWebsite(): ?Website
    {
        return $this->website;
    }

    public function setWebsite(?Website $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getGps(): ?Gps
    {
        return $this->gps;
    }

    public function setGps(?Gps $gps): self
    {
        $this->gps = $gps;

        return $this;
    }

    /**
     * @return Collection|Tagcat[]
     */
    public function getCatag(): Collection
    {
        return $this->catag;
    }

    public function addCatag(Tagcat $catag): self
    {
        if (!$this->catag->contains($catag)) {
            $this->catag[] = $catag;
            $catag->setHits($this);
        }

        return $this;
    }

    public function removeCatag(Tagcat $catag): self
    {
        if ($this->catag->removeElement($catag)) {
            // set the owning side to null (unless already changed)
            if ($catag->getHits() === $this) {
                $catag->setHits(null);
            }
        }

        return $this;
    }

    public function getLastdayshow(): ?\DateTimeInterface
    {
        return $this->lastdayshow;
    }

    public function setLastdayshow(?\DateTimeInterface $lastdayshow): self
    {
        $this->lastdayshow = $lastdayshow;

        return $this;
    }

}