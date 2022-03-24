<?php

namespace App\Entity\Websites;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Media\Background;
use App\Entity\Media\Pict;
use App\Entity\Sector\Sectors;
use App\Entity\UserMap\Taguery;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="aff_template")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\TemplateRepository")
 * @UniqueEntity(fields={"emailspaceweb"})
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"template-get-read"}}
 *     )
 */
class Template
{
  	/**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"buble", "edit_event"})
     * @ORM\OneToOne(targetEntity="App\Entity\Media\Pict", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $logo;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Media\Background", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $background;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\UserMap\Taguery", inversedBy="template", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $tagueries;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $baseline;

    /**
     * @var string $about
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $activities;

    /**
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     * @Assert\NotBlank()
     * @ORM\Column(type="string",  length=255, nullable=true)
     */
    private $emailspaceweb;

    /**
     * @ORM\Column(type="string", length=35, nullable=true)
     */
    private $telephonespaceweb;

    /**
     * @ORM\Column(type="string", length=35, nullable=true)
     */
    private $telephonemobspaceweb;

    /**
     *
     * @ORM\Column(type="boolean",  options={"default":false})
     */
    private $header =false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sector\Sectors", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"template-get-read","website_adress","customer_adress"})
     */
    private $sector;

    public function __construct()
    {
        $this->tagueries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getBaseline(): ?string
    {
        return $this->baseline;
    }

    public function setBaseline(?string $baseline): self
    {
        $this->baseline = $baseline;

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

    public function getActivities(): ?string
    {
        return $this->activities;
    }

    public function setActivities(?string $activities): self
    {
        $this->activities = $activities;

        return $this;
    }

    public function getLogo(): ?Pict
    {
        return $this->logo;
    }

    public function setLogo(?Pict $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getEmailspaceweb(): ?string
    {
        return $this->emailspaceweb;
    }

    public function setEmailspaceweb(?string $emailspaceweb): self
    {
        $this->emailspaceweb = $emailspaceweb;

        return $this;
    }

    public function getTelephonespaceweb()
    {
        return $this->telephonespaceweb;
    }

    public function setTelephonespaceweb($telephonespaceweb): self
    {
        $this->telephonespaceweb = $telephonespaceweb;

        return $this;
    }

    public function getTelephonemobspaceweb()
    {
        return $this->telephonemobspaceweb;
    }

    public function setTelephonemobspaceweb($telephonemobspaceweb): self
    {
        $this->telephonemobspaceweb = $telephonemobspaceweb;

        return $this;
    }

    public function getHeader(): ?bool
    {
        return $this->header;
    }

    public function setHeader(bool $header): self
    {
        $this->header = $header;

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
        if ($this->tagueries->contains($taguery)) {
            $this->tagueries->removeElement($taguery);
        }

        return $this;
    }
    /**
     * @return mixed
     */
    public function getBackground()
    {
        return $this->background;
    }

    /**
     * @param mixed $background
     * @return Template
     */
    public function setBackground($background)
    {
        $this->background = $background;
        return $this;
    }

}