<?php

namespace App\Entity\Sector;

use App\Entity\UserMap\Hits;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @ORM\Table(name="aff_gps")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\GpsRepository")
 * @UniqueEntity("nameloc")
 * @UniqueEntity("slugcity")
 */
class Gps
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $namefile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $nameloc;

    /**
     * @var string
     * @Groups({"buble"})
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $city;

    /**
     * @Groups({"buble"})
     * @ORM\Column(type="string", length=190, unique=true)
     */
    private $slugcity;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $insee;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $namecodep;

    /**
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $lonloc;

    /**
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $latloc;

    /**
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $perimeter;

    /**
     * @ORM\OneToMany (targetEntity="App\Entity\Sector\Adresses", mappedBy="gps")
     * @ORM\JoinColumn(nullable=true)
     */
    private $adresses;

    /**
     * @ORM\OneToMany (targetEntity="App\Entity\UserMap\Hits", mappedBy="gps")
     * @ORM\JoinColumn(nullable=true)
     */
    private $hits;

    public function __construct()
    {
        $this->adresses = new ArrayCollection();
        $this->hits = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function  __toString(): string
    {
        return $this->getNameloc();
    }

    public function gpsSlug(SluggerInterface $slugger)
    {
        if (!$this->slugcity || '-' === $this->slugcity) {
            $this->slugcity = (string) $slugger->slug((string) $this)->lower();
        }
    }

    public function getSlugcity(): ?string
    {
        return $this->slugcity;
    }

    public function getNameloc(): ?string
    {
        return $this->nameloc;
    }

    public function setNameloc(?string $nameloc): self
    {
        $this->nameloc = $nameloc;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getLonloc(): ?float
    {
        return $this->lonloc;
    }

    public function setLonloc(?float $lonloc): self
    {
        $this->lonloc = $lonloc;

        return $this;
    }

    public function getLatloc(): ?float
    {
        return $this->latloc;
    }

    public function setLatloc(?float $latloc): self
    {
        $this->latloc = $latloc;

        return $this;
    }

    public function getPerimeter(): ?float
    {
        return $this->perimeter;
    }

    public function setPerimeter(?float $perimeter): self
    {
        $this->perimeter = $perimeter;

        return $this;
    }

    public function getNamecodep(): ?string
    {
        return $this->namecodep;
    }

    public function setNamecodep(?string $namecodep): self
    {
        $this->namecodep = $namecodep;

        return $this;
    }
    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return Gps
     */
    public function setCode(string $code): Gps
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getInsee(): string
    {
        return $this->insee;
    }

    /**
     * @param string $insee
     * @return Gps
     */
    public function setInsee(string $insee): Gps
    {
        $this->insee = $insee;
        return $this;
    }

    public function setSlugcity(string $slugcity): self
    {
        $this->slugcity = $slugcity;

        return $this;
    }
    public function getUploadDir(): string
    {
        return 'cities';
    }

    public function getUploadRootDir(): string
    {
        // On retourne le chemin relatif vers l'image pour notre code PHP

        return  __DIR__.'/../../../public/'.$this->getUploadDir();

    }

    public function getWebPath(): string
    {
        return $this->getUploadDir().'/'.$this->namefile;
    }

    public function getNamefile(): ?string
    {
        return $this->namefile;
    }

    public function setNamefile(string $namefile): self
    {
        $this->namefile = $namefile;

        return $this;
    }

    /**
     * @return Collection|Adresses[]
     */
    public function getAdresses(): Collection
    {
        return $this->adresses;
    }

    public function addAdress(Adresses $adress): self
    {
        if (!$this->adresses->contains($adress)) {
            $this->adresses[] = $adress;
            $adress->setGps($this);
        }

        return $this;
    }

    public function removeAdress(Adresses $adress): self
    {
        if ($this->adresses->removeElement($adress)) {
            // set the owning side to null (unless already changed)
            if ($adress->getGps() === $this) {
                $adress->setGps(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Hits[]
     */
    public function getHits(): Collection
    {
        return $this->hits;
    }

    public function addHit(Hits $hit): self
    {
        if (!$this->hits->contains($hit)) {
            $this->hits[] = $hit;
            $hit->setGps($this);
        }

        return $this;
    }

    public function removeHit(Hits $hit): self
    {
        if ($this->hits->removeElement($hit)) {
            // set the owning side to null (unless already changed)
            if ($hit->getGps() === $this) {
                $hit->setGps(null);
            }
        }

        return $this;
    }

}