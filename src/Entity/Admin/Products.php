<?php


namespace App\Entity\Admin;

use App\Entity\Media\Pict;
use Doctrine\ORM\Mapping as ORM;

/**
 * Products
 *
 * @ORM\Table(name="aff_products")
 * @ORM\Entity("App\Repository\Entity\ProductsRepository")
 */
class Products
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
     * @ORM\OneToOne(targetEntity="App\Entity\Media\Pict", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $pict;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Admin\ProductCategories")
     * @ORM\JoinColumn(nullable=true)
     */
    private $categorie;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Admin\Tva", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $tva;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=125, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=false)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="unit", type="string", length=125, nullable=false)
     */
    private $unit;

    /**
     * @var boolean
     *
     * @ORM\Column(name="disponible", type="boolean", nullable=false)
     */
    private $disponible;

    /**
     * @var boolean
     *
     * @ORM\Column(name="remisable", type="boolean", nullable=false)
     */
    private $remisable;


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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getDisponible(): ?bool
    {
        return $this->disponible;
    }

    public function setDisponible(bool $disponible): self
    {
        $this->disponible = $disponible;

        return $this;
    }

    public function getRemisable(): ?bool
    {
        return $this->remisable;
    }

    public function setRemisable(bool $remisable): self
    {
        $this->remisable = $remisable;

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

    public function getCategorie(): ?ProductCategories
    {
        return $this->categorie;
    }

    public function setCategorie(?ProductCategories $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getTva(): ?Tva
    {
        return $this->tva;
    }

    public function setTva(?Tva $tva): self
    {
        $this->tva = $tva;

        return $this;
    }

}