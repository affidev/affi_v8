<?php


namespace App\Entity\Module;


use App\Entity\Admin\Wborders;
use App\Entity\Agenda\Appointments;
use App\Entity\LogMessages\PrivateConvers;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_reservato")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\ResarestoRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Resaresto
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Agenda\Appointments")
     */
    private $appointment;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $keymodule;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Admin\Wborders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $wborder;

    /**
     * @ORM\Column(type="string",  length=255, nullable=true)
     */
    private $salle;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\LogMessages\PrivateConvers")
     * @ORM\JoinColumn(nullable=true)
     */
    private $convers;

    /**
     * @ORM\Column(type="datetime", nullable=false))
     */
    private $dateresa_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $strtimeresa;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datecre_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datemodif_at;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $active = true;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $deleted = false;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $resaconfirmed=false;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $resarappel=false;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $resasend=false;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $resaread=false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logresa=false;


    public function __construct()
    {
        $this->datecre_at=new \DateTime();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSalle(): ?string
    {
        return $this->salle;
    }

    public function setSalle(?string $salle): self
    {
        $this->salle = $salle;

        return $this;
    }

    public function getDateresaAt(): ?\DateTimeInterface
    {
        return $this->dateresa_at;
    }

    public function setDateresaAt(\DateTimeInterface $dateresa_at): self
    {
        $this->dateresa_at = $dateresa_at;

        return $this;
    }

    public function getDatecreAt(): ?\DateTimeInterface
    {
        return $this->datecre_at;
    }

    public function setDatecreAt(\DateTimeInterface $datecre_at): self
    {
        $this->datecre_at = $datecre_at;

        return $this;
    }

    public function getDatemodifAt(): ?\DateTimeInterface
    {
        return $this->datemodif_at;
    }

    public function setDatemodifAt(?\DateTimeInterface $datemodif_at): self
    {
        $this->datemodif_at = $datemodif_at;

        return $this;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getResaconfirmed(): ?bool
    {
        return $this->resaconfirmed;
    }

    public function setResaconfirmed(bool $resaconfirmed): self
    {
        $this->resaconfirmed = $resaconfirmed;

        return $this;
    }

    public function getStrtimeresa(): ?string
    {
        return $this->strtimeresa;
    }

    public function setStrtimeresa(string $strtimeresa): self
    {
        $this->strtimeresa = $strtimeresa;

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

    public function getResarappel(): ?bool
    {
        return $this->resarappel;
    }

    public function setResarappel(bool $resarappel): self
    {
        $this->resarappel = $resarappel;

        return $this;
    }

    public function getResasend(): ?bool
    {
        return $this->resasend;
    }

    public function setResasend(bool $resasend): self
    {
        $this->resasend = $resasend;

        return $this;
    }

    public function getResaread(): ?bool
    {
        return $this->resaread;
    }

    public function setResaread(bool $resaread): self
    {
        $this->resaread = $resaread;

        return $this;
    }

    public function getLogresa(): ?string
    {
        return $this->logresa;
    }

    public function setLogresa(?string $logresa): self
    {
        $this->logresa = $logresa;

        return $this;
    }

    public function getAppointment(): ?Appointments
    {
        return $this->appointment;
    }

    public function setAppointment(?Appointments $appointment): self
    {
        $this->appointment = $appointment;

        return $this;
    }

    public function getWborder(): ?Wborders
    {
        return $this->wborder;
    }

    public function setWborder(Wborders $wborder): self
    {
        $this->wborder = $wborder;

        return $this;
    }

    public function getConvers(): ?PrivateConvers
    {
        return $this->convers;
    }

    public function setConvers(?PrivateConvers $convers): self
    {
        $this->convers = $convers;

        return $this;
    }

    public function getKeymodule(): ?string
    {
        return $this->keymodule;
    }

    public function setKeymodule(string $keymodule): self
    {
        $this->keymodule = $keymodule;

        return $this;
    }
}