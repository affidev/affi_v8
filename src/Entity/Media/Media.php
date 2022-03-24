<?php

namespace App\Entity\Media;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="aff_media")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\MediaRepository")
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"media_post:read"}}
 *)
 */
class Media
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"simplebuble"})
     * @Groups({"media_post:read","offres_post:read","postation_post:read","module_post:read","website_post:read","edit_event","event_post:read"})
     * @ORM\OneToMany(targetEntity="\App\Entity\Media\Imagejpg", mappedBy="idmedia", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $imagejpg;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $extension;

    /**
     * @ORM\OneToOne(targetEntity="\App\Entity\Media\Pdfstore", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $pdfstore;


    public function __construct()
    {

        $this->imagejpg = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Imagejpg[]
     */
    public function getImagejpg(): Collection
    {
        return $this->imagejpg;
    }

    public function addImagejpg(Imagejpg $imagejpg): self
    {
        if (!$this->imagejpg->contains($imagejpg)) {
            $this->imagejpg[] = $imagejpg;
            $imagejpg->setIdmedia($this);
        }

        return $this;
    }

    public function removeImagejpg(Imagejpg $imagejpg): self
    {
        if ($this->imagejpg->contains($imagejpg)) {
            $this->imagejpg->removeElement($imagejpg);
            // set the owning side to null (unless already changed)
            if ($imagejpg->getIdmedia() === $this) {
                $imagejpg->setIdmedia(null);
            }
        }

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getPdfstore(): ?PdfStore
    {
        return $this->pdfstore;
    }

    public function setPdfstore(?PdfStore $pdfstore): self
    {
        $this->pdfstore = $pdfstore;

        return $this;
    }
}