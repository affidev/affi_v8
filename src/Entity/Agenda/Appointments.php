<?php

namespace App\Entity\Agenda;


use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Sector\Gps;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use \DateTime;

/**
 * @ORM\Table(name="aff_appointments")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\AppointmentsRepository")
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"appointoffre:read"}}
 *)
 */
class Appointments
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $TypeAppointment;

    /**
     * @Groups({"appointoffre:read", "formules_post:read","edit_event","event_post:read"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $starttime;

    /**
     * @Groups({"appointoffre:read", "formules_post:read","edit_event","event_post:read"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endtime;

    /**
     * @ORM\OneToMany(targetEntity="\App\Entity\Agenda\Periods", mappedBy="idAppointment")
     * @ORM\JoinColumn(nullable=true)
     */
    private $idPeriods;

    /**
     * @Groups({"appointoffre:read", "formules_post:read","edit_event"})
     * @ORM\OneToOne(targetEntity="\App\Entity\Agenda\Tabdate", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $tabdate;

    /**
     * @ORM\Column(type="integer")
     */
    private $statut;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $confirmed;  

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Agenda\CallbacksAppoint", mappedBy="idAppointmentCb")
     * @ORM\JoinColumn(nullable=true)
    */
    private $frequenceCallbacks;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sector\Gps")
     * @ORM\JoinColumn(nullable=false)
     */
    private $localisation;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datemaj_at;

    public function __construct()
    {
        $this->idPeriods = new ArrayCollection();
        $this->frequenceCallbacks = new ArrayCollection();
        $this->create_at=new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStarttime(): ?\DateTimeInterface
    {
        return $this->starttime;
    }

    public function setStarttime(?\DateTimeInterface $starttime): self
    {
        $this->starttime = $starttime;

        return $this;
    }

    public function getEndtime(): ?\DateTimeInterface
    {
        return $this->endtime;
    }

    public function setEndtime(?\DateTimeInterface $endtime): self
    {
        $this->endtime = $endtime;

        return $this;
    }

    public function getStatut(): ?int
    {
        return $this->statut;
    }

    public function setStatut(int $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getConfirmed(): ?bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(?bool $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    /**
     * @return Collection|Periods[]
     */
    public function getIdPeriods(): Collection
    {
        return $this->idPeriods;
    }

    public function addIdPeriod(Periods $idPeriod): self
    {
        if (!$this->idPeriods->contains($idPeriod)) {
            $this->idPeriods[] = $idPeriod;
            $idPeriod->setIdAppointment($this);
        }

        return $this;
    }

    public function removeIdPeriod(Periods $idPeriod): self
    {
        if ($this->idPeriods->contains($idPeriod)) {
            $this->idPeriods->removeElement($idPeriod);
            // set the owning side to null (unless already changed)
            if ($idPeriod->getIdAppointment() === $this) {
                $idPeriod->setIdAppointment(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CallbacksAppoint[]
     */
    public function getFrequenceCallbacks(): Collection
    {
        return $this->frequenceCallbacks;
    }

    public function addFrequenceCallback(CallbacksAppoint $frequenceCallback): self
    {
        if (!$this->frequenceCallbacks->contains($frequenceCallback)) {
            $this->frequenceCallbacks[] = $frequenceCallback;
            $frequenceCallback->setIdAppointmentCb($this);
        }

        return $this;
    }

    public function removeFrequenceCallback(CallbacksAppoint $frequenceCallback): self
    {
        if ($this->frequenceCallbacks->contains($frequenceCallback)) {
            $this->frequenceCallbacks->removeElement($frequenceCallback);
            // set the owning side to null (unless already changed)
            if ($frequenceCallback->getIdAppointmentCb() === $this) {
                $frequenceCallback->setIdAppointmentCb(null);
            }
        }

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

    public function getTypeAppointment(): ?int
    {
        return $this->TypeAppointment;
    }

    public function setTypeAppointment(int $TypeAppointment): self
    {
        $this->TypeAppointment = $TypeAppointment;

        return $this;
    }

    public function getLocalisation(): ?Gps
    {
        return $this->localisation;
    }

    public function setLocalisation(?Gps $localisation): self
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getTabdate(): ?Tabdate
    {
        return $this->tabdate;
    }

    public function setTabdate(?Tabdate $tabdate): self
    {
        $this->tabdate = $tabdate;

        return $this;
    }

}
