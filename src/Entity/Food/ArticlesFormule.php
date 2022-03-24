<?php

namespace App\Entity\Food;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Media\Pict;
use App\Entity\Module\Formules;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="aff_articlesformule", indexes={@ORM\Index(name="search_keymodule", columns={"keymodule"} )} )
 * @ORM\Entity(repositoryClass="App\Repository\Entity\ArticlesFormuleRepository")
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"artformule_post:read"}}
 *)
 */

class ArticlesFormule
{
    /**
     * @Groups({"artformule_post:read","formules_post:read"})
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $keymodule;

    /**
     * @Groups({"artformule_post:read","formules_post:read"})
     * @ORM\Column(type="string", length=190, nullable=false)
     */
    private $titre;

    /**
     * @Groups({"artformule_post:read","formules_post:read"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Food\Categories", inversedBy="artform")
     */
    private $categorie;

    /**
     * @Groups({"artformule_post:read","formules_post:read"})
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $composition;

    /**
     * @Groups({"artformule_post:read","formules_post:read"})
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $descriptif;

    /**
     * @Groups({"artformule_post:read","formules_post:read"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Media\Pict", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $pict;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Module\Formules", mappedBy="articles")
     * @ORM\JoinColumn(nullable=true)
     */
    private $formules;


    /**
     * @Groups({"artformule_post:read","formules_post:read"})
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $deleted = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"artformule_post:read","formules_post:read"})
     */
    private $prix;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"artformule_post:read","formules_post:read"})
     */
    private $infos;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     * @Groups({"artformule_post:read","formules_post:read"})
     */
    private $active = true;

    public function __construct()
    {
        $this->formules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($null)
    {
        $this->id=null;
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

    public function getCategorie()
    {
        return $this->categorie;
    }

    public function setCategorie(Categories $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getComposition(): ?string
    {
        return $this->composition;
    }

    public function setComposition(?string $composition): self
    {
        $this->composition = $composition;

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

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getPict(): ?Pict
    {
        return $this->pict;
    }

    public function setPict(?Pict $pict): self
    {
        $this->pict = $pict;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(?string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getInfos(): ?string
    {
        return $this->infos;
    }

    public function setInfos(string $infos): self
    {
        $this->infos = $infos;

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
     * @return Collection|Formules[]
     */
    public function getFormules(): Collection
    {
        return $this->formules;
    }

    public function addFormule(Formules $formule): self
    {
        if (!$this->formules->contains($formule)) {
            $this->formules[] = $formule;
            $formule->addArticle($this);
        }

        return $this;
    }

    public function removeFormule(Formules $formule): self
    {
        if ($this->formules->contains($formule)) {
            $this->formules->removeElement($formule);
            $formule->removeArticle($this);
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