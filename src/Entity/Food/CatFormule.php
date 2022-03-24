<?php


namespace App\Entity\Food;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Module\Formules;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="App\Repository\Entity\CatFormuleRepository")
 *
 * @UniqueEntity("name")
 *  @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"catformule_post:read"}}
 *)
 */
class CatFormule
{
    const SERVICE = [
        0 => 'midi',
        1 => 'soir',
        2 => 'midi et soir'
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Length(
     * min=5,
     * max=50,
     * min="le titre doit faire au moins {{ limit }} caractères",
     * max="le titre doit faire au maximum {{ limit }} caractères")
     * @Groups({"catformule_post:read","formules_post:read"})
     * @ORM\Column(type="string", length=190, nullable=true))
     */
    private $name;

    /**
     * @Assert\Range(
     * min=0,
     * max=100,
     * min="le prix ne peut être égal à zero",
     * max="le prix ne peut pas être supérieur à 100 €"
     * )
     * @Assert\NotBlank
     * @ORM\Column(type="float")
     * @Groups({"catformule_post:read","formules_post:read"})
     */
    private $prix;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"catformule_post:read","formules_post:read"})
     */
    private $description;

    /**
     * @Groups({"catformule_post:read","formules_post:read", "declinaison_post:read"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Food\Declinaison")
     */
    private $declinaison;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     * @Groups({"catformule_post:read"})
     */
    private $boisson = false;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datemaj_at;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     * @Groups({"catformule_post:read"})
     */
    private $active = true;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Module\Formules", inversedBy="catformules")
     */
    private $formule;


    public function __construct()
    {
        $this->create_at = new \DateTime();
    }

    public function getFormatedPrix(): string
    {
        return number_format($this->prix, 2, ',', ' ');
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

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

    public function getBoisson(): ?bool
    {
        return $this->boisson;
    }

    public function setBoisson(bool $boisson): self
    {
        $this->boisson = $boisson;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->create_at;
    }

    public function setCreateAt(\DateTimeInterface $create_at): self
    {
        $this->create_at = $create_at;

        return $this;
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

    public function getFormule(): ?Formules
    {
        return $this->formule;
    }

    public function setFormule(?Formules $formule): self
    {
        $this->formule = $formule;

        return $this;
    }

    public function getDeclinaison(): ?Declinaison
    {
        return $this->declinaison;
    }

    public function setDeclinaison(?Declinaison $declinaison): self
    {
        $this->declinaison = $declinaison;

        return $this;
    }

}