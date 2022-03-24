<?php

namespace App\Entity\Sector;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 *
 * @ORM\Table(name="aff_sector")
 * @ORM\Entity(repositoryClass="App\Repository\SectorsRepository")
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"sector-get-read"}}
 *     )
 */
class Sectors
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Sector\Adresses", mappedBy="sector", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"sector-get-read","template-get-read","website_adress","customer_adress","edit_event"})
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $infosecteur;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=8, nullable=true)
     */
    private $codesector;

    public function __construct()
    {
        $this->adresse = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInfosecteur(): ?string
    {
        return $this->infosecteur;
    }

    public function setInfosecteur(?string $infosecteur): self
    {
        $this->infosecteur = $infosecteur;

        return $this;
    }

    public function getCodesector(): ?string
    {
        return $this->codesector;
    }

    public function setCodesector(?string $codesector): self
    {
        $this->codesector = $codesector;

        return $this;
    }

    /**
     * @return Collection|Adresses[]
     */
    public function getAdresse(): Collection
    {
        return $this->adresse;
    }

    public function addAdresse(Adresses $adresse): self
    {
        if (!$this->adresse->contains($adresse)) {
            $this->adresse[] = $adresse;
            $adresse->addSector($this);
        }

        return $this;
    }

    public function removeAdresse(Adresses $adresse): self
    {
        if ($this->adresse->removeElement($adresse)) {
            $adresse->removeSector($this);
        }

        return $this;
    }

}
