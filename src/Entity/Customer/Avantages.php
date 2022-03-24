<?php


namespace App\Entity\Customer;


use App\Entity\DispatchSpace\DispatchSpaceWeb;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="aff_avantages")
 * @ORM\Entity(repositoryClass="App\Repository\AvantagesRepository")
 */
class Avantages
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Length(
     * min=10,
     * max=50,
     * minMessage="le titre doit faire au moins {{ limit }} caractères",
     * maxMessage="le titre doit faire au maximum {{ limit }} caractères")
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=190)
     */
    private $descriptif;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $remise;

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
     */
    private $active = true;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DispatchSpace\DispatchSpaceWeb", inversedBy="avantages")
     */
    private $dispatch;


    public function __construct()
    {
        $this->create_at=new \DateTime();
    }

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

    public function getDescriptif(): ?string
    {
        return $this->descriptif;
    }

    public function setDescriptif(string $descriptif): self
    {
        $this->descriptif = $descriptif;

        return $this;
    }

    public function getRemise(): ?int
    {
        return $this->remise;
    }

    public function setRemise(?int $remise): self
    {
        $this->remise = $remise;

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

    public function getDispatch(): ?DispatchSpaceWeb
    {
        return $this->dispatch;
    }

    public function setDispatch(?DispatchSpaceWeb $dispatch): self
    {
        $this->dispatch = $dispatch;

        return $this;
    }
}