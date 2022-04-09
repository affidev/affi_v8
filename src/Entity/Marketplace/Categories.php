<?php


namespace App\Entity\Marketplace;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="aff_offre_categories")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\OffreCategoriesRepository")
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"category_offre:read"}}
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
     * @Groups({"category_post:read","offres_post:read"})
     * @ORM\Column(type="string", length=255)
     */
    private $name;


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
}