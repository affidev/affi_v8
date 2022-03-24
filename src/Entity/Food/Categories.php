<?php


namespace App\Entity\Food;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Entity\CategoriesRepository")
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"category_post:read"}}
 *)
 */
class Categories
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"category_post:read","artformule_post:read","formules_post:read"})
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Food\ArticlesFormule", mappedBy="categorie")
     */
    private $artform;

    public function __construct()
    {
        $this->artform = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function  __toString(): string
    {
        return $this->getName();
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

    /**
     * @return Collection
     */
    public function getArtform(): Collection
    {
        return $this->artform;
    }

    public function addArtform(ArticlesFormule $artform): self
    {
        if (!$this->artform->contains($artform)) {
            $this->artform[] = $artform;
            $artform->setCategorie($this);
        }

        return $this;
    }

    public function removeArtform(ArticlesFormule $artform): self
    {
        if ($this->artform->contains($artform)) {
            $this->artform->removeElement($artform);
            // set the owning side to null (unless already changed)
            if ($artform->getCategorie() === $this) {
                $artform->setCategorie(null);
            }
        }

        return $this;
    }

}