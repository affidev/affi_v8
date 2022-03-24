<?php


namespace App\Entity\Sector;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="aff_adresses")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\AdressesRepository")
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"adress-get-read"}}
 *     )
 */
class Adresses
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups({"edit_event"})
     */
    private $id;

     /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Sector\Sectors", inversedBy="adresse")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sector;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sector\Gps", inversedBy="adresses")
     * @ORM\JoinColumn(nullable=true)
     */
    private $gps;

    /**
     * @var string
     *
     * @ORM\Column(name="idmap", type="string", length=25, nullable=true)
     */
    private $idMap;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Groups({"adress-get-read","sector-get-read","template-get-read","website_adress"})
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"adress-get-read","sector-get-read","template-get-read","website_adress"})
     */
    private $nom_voie;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=7, nullable=true)
     * @Groups({"adress-get-read","sector-get-read","template-get-read","website_adress"})
     */
    private $code_postal;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $insee;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Groups({"adress-get-read","sector-get-read","template-get-read","website_adress"})
     */
    private $nom_commune;

    /**
     *
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"adress-get-read","sector-get-read","template-get-read","website_adress"})
     */
    private $lon;

    /**
     *
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"adress-get-read","sector-get-read","template-get-read","website_adress"})
     */
    private $lat;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=125, nullable=true)
     */
    private $pays;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=125, nullable=true)
     * @Groups({"adress-get-read","sector-get-read","template-get-read","website_adress"})
     */
    private $typeadress;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"edit_event"})
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $departement;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $numdepart;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $region;

    /**
     * @var string
     *
     * @ORM\Column(type="integer", options={"default=1"})
     * @Groups({"adress-get-read","sector-get-read","template-get-read","website_adress"})
     */
    private $choiceadress=1;

    public function __construct()
    {
        $this->sector = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdMap(): ?string
    {
        return $this->idMap;
    }

    public function setIdMap(?string $idMap): self
    {
        $this->idMap = $idMap;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getNomVoie(): ?string
    {
        return $this->nom_voie;
    }

    public function setNomVoie(?string $nom_voie): self
    {
        $this->nom_voie = $nom_voie;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(?string $code_postal): self
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getNomCommune(): ?string
    {
        return $this->nom_commune;
    }

    public function setNomCommune(?string $nom_commune): self
    {
        $this->nom_commune = $nom_commune;

        return $this;
    }

    public function getLon(): ?float
    {
        return $this->lon;
    }

    public function setLon(?float $lon): self
    {
        $this->lon = $lon;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(?float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getTypeadress(): ?string
    {
        return $this->typeadress;
    }

    public function setTypeadress(string $typeadress): self
    {
        $this->typeadress = $typeadress;

        return $this;
    }

    public function getDepartement(): ?string
    {
        return $this->departement;
    }

    public function setDepartement(?string $departement): self
    {
        $this->departement = $departement;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getInsee(): ?string
    {
        return $this->insee;
    }

    public function setInsee(?string $insee): self
    {
        $this->insee = $insee;

        return $this;
    }

    public function getNumdepart(): ?string
    {
        return $this->numdepart;
    }

    public function setNumdepart(?string $numdepart): self
    {
        $this->numdepart = $numdepart;

        return $this;
    }

    public function getChoiceadress(): ?int
    {
        return $this->choiceadress;
    }

    public function setChoiceadress(int $choiceadress): self
    {
        $this->choiceadress = $choiceadress;

        return $this;
    }

    /**
     * @return Collection|Sectors[]
     */
    public function getSector(): Collection
    {
        return $this->sector;
    }

    public function addSector(Sectors $sector): self
    {
        if (!$this->sector->contains($sector)) {
            $this->sector[] = $sector;
        }

        return $this;
    }

    public function removeSector(Sectors $sector): self
    {
        $this->sector->removeElement($sector);

        return $this;
    }

    public function getGps(): ?Gps
    {
        return $this->gps;
    }

    public function setGps(?Gps $gps): self
    {
        $this->gps = $gps;

        return $this;
    }


}