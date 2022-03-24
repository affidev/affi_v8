<?php

namespace App\Entity\Module;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Agenda\Appointments;
use App\Entity\Food\ArticlesFormule;
use App\Entity\Food\CatFormule;
use App\Entity\Food\Service;
use App\Entity\Media\Pict;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use DateTimeImmutable;

/**
 * @ORM\Table(name="aff_formules", indexes={@ORM\Index(columns={"keymodule"})})
 * @ORM\Entity(repositoryClass="App\Repository\Entity\FormulesRepository")
 * @ApiResource(
 *      collectionOperations={
 *          "get"={"path"="/ressources/affi/formule/allfind/"}},
 *     itemOperations={
 *          "get"={"path"="/ressources/affi/formule/find/{id}"}},
 *     normalizationContext={"groups"={"formules_post:read","formules_post_full:read"}}
 *)
 * @ApiFilter(BooleanFilter::class, properties={"publied"})
 * @ApiFilter(OrderFilter::class, properties={"createAt": "DESC"})
 * @ApiFilter(SearchFilter::class, properties={"keymodule": "exact"})
 */

class Formules
{

    /**
     * @Groups({"formules_post:read"})
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"formules_post:read"})
     * @ORM\Column(type="string", length=190, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @Groups({"formules_post:read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @Groups({"formules_post:read"})
     * @ORM\OneToOne(targetEntity="App\Entity\Media\Pict", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $pictformule;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $keymodule;

    /**
     * @Groups({"formules_post:read","appointoffre:read"})
     * @ORM\OneToOne(targetEntity="App\Entity\Agenda\Appointments", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $parution;

    /**
     * @Groups({"formules_post:read","artformule_post:read"})
     * @ORM\ManyToMany(targetEntity="App\Entity\Food\ArticlesFormule", inversedBy="formules")
     * @ORM\JoinColumn(nullable=true)
     */
    private $articles;

    /**
     * @Groups({"formules_post:read"})
     * @ORM\OneToMany(targetEntity="App\Entity\Food\CatFormule", mappedBy="formule", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $catformules;

    /**
     * @Groups({"formules_post:read"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Food\Service")
     */
    private $services;

    /**
     * @Groups({"formules_post:read"})
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $deleted = false;

    /**
     * @Groups({"formules_post:read"})
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $publied = false;

    /**
     * @Groups({"formules_post:read"})
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    /**
     * @Groups({"formules_post:read"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datemaj_at;

    /**
     * @Groups({"formules_post:read"})
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $active = true;

    public function __construct()
    {
        $this->createAt=new DateTimeImmutable();
        $this->articles = new ArrayCollection();
        $this->catformules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getPictformule(): ?Pict
    {
        return $this->pictformule;
    }

    public function setPictformule(?Pict $pictformule): self
    {
        $this->pictformule = $pictformule;

        return $this;
    }

    public function getParution(): ?Appointments
    {
        return $this->parution;
    }

    public function setParution(?Appointments $parution): self
    {
        $this->parution = $parution;

        return $this;
    }

    public function setId($null): Formules
    {
        $this->id=null;
        return $this;
    }

    public function setPublied(bool $publied): Formules
    {
        $this->publied=$publied;
        return $this;
    }

    public function getPublied(): ?bool
    {
        return $this->publied;
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


    /**
     * @return Collection|CatFormule[]
     */
    public function getCatformules(): Collection
    {
        return $this->catformules;
    }

    public function addCatformule(CatFormule $catformule): self
    {
        if (!$this->catformules->contains($catformule)) {
            $this->catformules[] = $catformule;
            $catformule->setFormule($this);
        }

        return $this;
    }

    public function removeCatformule(CatFormule $catformule): self
    {
        if ($this->catformules->contains($catformule)) {
            $this->catformules->removeElement($catformule);
            // set the owning side to null (unless already changed)
            if ($catformule->getFormule() === $this) {
                $catformule->setFormule(null);
            }
        }

        return $this;
    }

    public function getServices(): ?Service
    {
        return $this->services;
    }

    public function setServices(?Service $services): self
    {
        $this->services = $services;

        return $this;
    }

    /**
     * @return Collection|ArticlesFormule[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(ArticlesFormule $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
        }

        return $this;
    }

    public function removeArticle(ArticlesFormule $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
        }

        return $this;
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

}