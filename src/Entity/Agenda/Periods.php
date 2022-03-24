<?php

namespace App\Entity\Agenda;


use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_periods")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\PeriodsRepository")
 */
class Periods
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\App\Entity\Agenda\Appointments", inversedBy="idPeriods")
     */
    private $idAppointment;

    /**
     * @ORM\Column(type="smallint", nullable=false)
     */
    private $periodeChoice;

    /**
     * @ORM\Column(type="smallint", nullable=false)
     */
    private $numberrept;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $typerept;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $daysweek;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $daymonth;
           
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startPeriod;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $alongPeriod;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPeriodeChoice(): ?int
    {
        return $this->periodeChoice;
    }

    public function setPeriodeChoice(int $periodeChoice): self
    {
        $this->periodeChoice = $periodeChoice;

        return $this;
    }

    public function getNumberrept(): ?int
    {
        return $this->numberrept;
    }

    public function setNumberrept(int $numberrept): self
    {
        $this->numberrept = $numberrept;

        return $this;
    }

    public function getTyperept(): ?int
    {
        return $this->typerept;
    }

    public function setTyperept(?int $typerept): self
    {
        $this->typerept = $typerept;

        return $this;
    }

    public function getDaysweek(): ?string
    {
        return $this->daysweek;
    }

    public function setDaysweek(?string $daysweek): self
    {
        $this->daysweek = $daysweek;

        return $this;
    }

    public function getDaymonth(): ?string
    {
        return $this->daymonth;
    }

    public function setDaymonth(?string $daymonth): self
    {
        $this->daymonth = $daymonth;

        return $this;
    }

    public function getStartPeriod(): ?\DateTimeInterface
    {
        return $this->startPeriod;
    }

    public function setStartPeriod(?\DateTimeInterface $startPeriod): self
    {
        $this->startPeriod = $startPeriod;

        return $this;
    }

    public function getAlongPeriod(): ?string
    {
        return $this->alongPeriod;
    }

    public function setAlongPeriod(?string $alongPeriod): self
    {
        $this->alongPeriod = $alongPeriod;

        return $this;
    }

    public function getIdAppointment(): ?Appointments
    {
        return $this->idAppointment;
    }

    public function setIdAppointment(?Appointments $idAppointment): self
    {
        $this->idAppointment = $idAppointment;

        return $this;
    }

    
}
