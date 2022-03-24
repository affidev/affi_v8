<?php


namespace App\Entity\Module;


use DateTime;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_reservations")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\ResaRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Reservation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $keymodule;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $active = true;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $deleted = false;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datemaj_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkone;


    public function __construct()
    {
        $this->create_at = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

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

    public function getLinkone(): ?string
    {
        return $this->linkone;
    }

    public function setLinkone(?string $linkone): self
    {
        $this->linkone = $linkone;

        return $this;
    }

}