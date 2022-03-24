<?php

namespace App\Entity\Customer;

use App\Entity\Admin\NumClients;
use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\User;
use App\Entity\UserMap\Heuristiques;
use App\Entity\Users\Contacts;
use App\Entity\Users\ProfilUser;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Table(name="aff_customers")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\CustomersRepository")
 */
class Customers
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Admin\NumClients", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $numclient;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $client=false;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\DispatchSpace\DispatchSpaceWeb", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $dispatchspace;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Users\ProfilUser", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $profil;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $active = true;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $charte = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $charte_at;

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
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $emailcontact;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserMap\Heuristiques", mappedBy="customer")
     * @ORM\JoinColumn(nullable=false)
     */
    private $heuristique;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Customer\Services", mappedBy="customer", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $services;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Users\Contacts")
     * @ORM\JoinColumn(nullable=true)
     */
    private $oldcontact;


    public function __construct()
    {
        $this->create_at=new DateTime();
        $this->heuristique = new ArrayCollection();
        $this->services = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmailcontact(): ?string
    {
        return $this->emailcontact;
    }

    public function setEmailcontact(?string $emailcontact): self
    {
        $this->emailcontact = $emailcontact;

        return $this;
    }

    public function getNumclient(): ?NumClients
    {
        return $this->numclient;
    }

    public function setNumclient(NumClients $numclient): self
    {
        $this->numclient = $numclient;

        return $this;
    }

    public function getCharte(): ?bool
    {
        return $this->charte;
    }

    public function setCharte(bool $charte): self
    {
        $this->charte = $charte;

        return $this;
    }

    public function getCharteAt(): ?\DateTimeInterface
    {
        return $this->charte_at;
    }

    public function setCharteAt(\DateTimeInterface $charte_at): self
    {
        $this->charte_at = $charte_at;

        return $this;
    }

    public function getDispatchspace(): ?DispatchSpaceWeb
    {
        return $this->dispatchspace;
    }

    public function setDispatchspace(?DispatchSpaceWeb $dispatchspace): self
    {
        $this->dispatchspace = $dispatchspace;

        return $this;
    }

    public function getClient(): ?bool
    {
        return $this->client;
    }

    public function setClient(bool $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getProfil(): ?ProfilUser
    {
        return $this->profil;
    }

    public function setProfil(ProfilUser $profil): self
    {
        $this->profil = $profil;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getHeuristique(): Collection
    {
        return $this->heuristique;
    }

    public function addHeuristique(Heuristiques $heuristique): self
    {
        if (!$this->heuristique->contains($heuristique)) {
            $this->heuristique[] = $heuristique;
            $heuristique->setCustomer($this);
        }

        return $this;
    }

    public function removeHeuristique(Heuristiques $heuristique): self
    {
        if ($this->heuristique->contains($heuristique)) {
            $this->heuristique->removeElement($heuristique);
            // set the owning side to null (unless already changed)
            if ($heuristique->getCustomer() === $this) {
                $heuristique->setCustomer(null);
            }
        }

        return $this;
    }

    public function getOldcontact(): ?Contacts
    {
        return $this->oldcontact;
    }

    public function setOldcontact(?Contacts $oldcontact): self
    {
        $this->oldcontact = $oldcontact;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Services $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->setCustomer($this);
        }

        return $this;
    }

    public function removeService(Services $service): self
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getCustomer() === $this) {
                $service->setCustomer(null);
            }
        }

        return $this;
    }

}