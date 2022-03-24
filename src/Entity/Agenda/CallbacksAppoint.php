<?php

namespace App\Entity\Agenda;


use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_callbackappoint")
 * @ORM\Entity
 */
class CallbacksAppoint
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Agenda\Appointments", inversedBy="frequenceCallbacks")
     */
    private $idAppointmentCb;

    /**
     * @ORM\Column(type="smallint", nullable=false)
     */
    private $choiceCallback;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChoiceCallback(): ?int
    {
        return $this->choiceCallback;
    }

    public function setChoiceCallback(int $choiceCallback): self
    {
        $this->choiceCallback = $choiceCallback;

        return $this;
    }

    public function getIdAppointmentCb(): ?Appointments
    {
        return $this->idAppointmentCb;
    }

    public function setIdAppointmentCb(?Appointments $idAppointmentCb): self
    {
        $this->idAppointmentCb = $idAppointmentCb;

        return $this;
    }

}
