<?php


namespace App\Entity\Admin;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="aff_factures")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\FacturesRepository")
 * @UniqueEntity("numfact")
 */
class Factures
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
     * @ORM\OneToOne(targetEntity="App\Entity\Admin\Wborders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $orders;

    /**
     * @var boolean
     *
     * @ORM\Column(name="valider", type="boolean", options={"default":false})
     */
    private $valider=false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="float", nullable=false)
     */
    private $montantttc;

    /**
     * @var boolean
     *
     * @ORM\Column(name="solde", type="boolean", options={"default":false})
     */
    private $solde=false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="accompte", type="boolean", options={"default":false})
     */
    private $accompte=false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datereglement", type="datetime")
     */
    private $datereglement;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateraccompte", type="datetime", nullable=true)
     */
    private $dateraccompte;

    /**
     * @var integer
     *
     * @ORM\Column( type="integer", nullable=false)
     */
    private $numfact;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $pdfacture;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_at;

    public function __construct()
    {
        $this->datereglement = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValider(): ?bool
    {
        return $this->valider;
    }

    public function setValider(bool $valider): self
    {
        $this->valider = $valider;

        return $this;
    }

    public function getSolde(): ?bool
    {
        return $this->solde;
    }

    public function setSolde(bool $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    public function getAccompte(): ?bool
    {
        return $this->accompte;
    }

    public function setAccompte(bool $accompte): self
    {
        $this->accompte = $accompte;

        return $this;
    }

    public function getDatereglement(): ?\DateTimeInterface
    {
        return $this->datereglement;
    }

    public function setDatereglement(\DateTimeInterface $datereglement): self
    {
        $this->datereglement = $datereglement;

        return $this;
    }

    public function getDateraccompte(): ?\DateTimeInterface
    {
        return $this->dateraccompte;
    }

    public function setDateraccompte(\DateTimeInterface $dateraccompte): self
    {
        $this->dateraccompte = $dateraccompte;

        return $this;
    }

    public function getNumfact(): ?int
    {
        return $this->numfact;
    }

    public function setNumfact(int $numfact): self
    {
        $this->numfact = $numfact;

        return $this;
    }

    public function getOrders(): ?Wborders
    {
        return $this->orders;
    }

    public function setOrders(Wborders $orders): self
    {
        $this->orders = $orders;

        return $this;
    }

    public function getPdfacture(): ?string
    {
        return $this->pdfacture;
    }

    public function setPdfacture(?string $pdfacture): self
    {
        $this->pdfacture = $pdfacture;

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

    public function getMontantttc(): ?float
    {
        return $this->montantttc;
    }

    public function setMontantttc(float $montantttc): self
    {
        $this->montantttc = $montantttc;

        return $this;
    }

}