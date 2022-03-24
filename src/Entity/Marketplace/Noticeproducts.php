<?php


namespace App\Entity\Marketplace;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\UserMap\Taguery;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Table(name="aff_noticeproduct")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\NoticeproductsRepository")
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"product_offre:read"}}
 *)
 */
class Noticeproducts
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
     * @var string
     * @Groups({"product_offre:read","offres_post:read","parutions:read", "simplebuble"})
     * @ORM\Column(type="string", length=125, nullable=true)
     */
    private $nameproduct;

    /**
     * @var string
     *
     * @Groups({"product_offre:read","offres_post:read","parutions:read", "simplebuble"})
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $idproduct;

    /**
     * *@Assert\Url(
     *     message = "l'url '{{ value }}' n'est pas valide."
     * )
     *
     * @Groups({"product_offre:read","offres_post:read","parutions:read", "simplebuble"})
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $urlproduct;

    /**
     * @var string
     *
     * @Groups({"product_offre:read","offres_post:read","parutions:read", "simplebuble"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $tabcarac;

    /**
     * @Groups({"product_offre:read","offres_post:read","parutions:read", "simplebuble"})
     * @ORM\OneToOne(targetEntity="App\Entity\Marketplace\DescriptProduct", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $htmlcontent;

    /**
     * @Groups({"offres_post:read","parutions:read", "simplebuble"})
     * @ORM\ManyToMany(targetEntity="App\Entity\UserMap\Taguery", inversedBy="noticeproducts", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $tagueries;

    /**
     * @var float
     *
     * @Groups({"product_offre:read","offres_post:read","parutions:read" ,"simplebuble"})
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @var float
     *
     * @Groups({"product_offre:read","offres_post:read","parutions:read", "simplebuble"})
     * @ORM\Column(type="float", nullable=true)
     */
    private $oldprice;

    /**
     * @var string
     *
     * @Groups({"product_offre:read","offres_post:read","parutions:read", "simplebuble"})
     * @ORM\Column(type="string", length=125, nullable=true)
     */
    private $unit;

    /**
     * @var boolean
     *
     * @Groups({"product_offre:read","offres_post:read","parutions:read"})
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $disponible=true;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $remisable=true;

    /**
     * @Groups({"offres_post:read"})
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    public function __construct()
    {
        $this->createAt=new DateTimeImmutable();
        $this->tagueries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameproduct(): ?string
    {
        return $this->nameproduct;
    }

    public function setNameproduct(string $nameproduct): self
    {
        $this->nameproduct = $nameproduct;

        return $this;
    }

    public function getIdproduct(): ?string
    {
        return $this->idproduct;
    }

    public function setIdproduct(?string $idproduct): self
    {
        $this->idproduct = $idproduct;

        return $this;
    }

    public function getUrlproduct(): ?string
    {
        return $this->urlproduct;
    }

    public function setUrlproduct(?string $urlproduct): self
    {
        $this->urlproduct = $urlproduct;

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

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getOldprice(): ?float
    {
        return $this->oldprice;
    }

    public function setOldprice(?float $oldprice): self
    {
        $this->oldprice = $oldprice;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): self
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

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getHtmlcontent(): ?DescriptProduct
    {
        return $this->htmlcontent;
    }

    public function setHtmlcontent(?DescriptProduct $htmlcontent): self
    {
        $this->htmlcontent = $htmlcontent;

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

    public function getTabcarac(): ?string
    {
        return $this->tabcarac;
    }

    public function setTabcarac(string $tabcarac): self
    {
        $this->tabcarac = $tabcarac;

        return $this;
    }
}