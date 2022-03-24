<?php


namespace App\Entity\Users;


use App\Entity\DispatchSpace\Tballmessage;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Table(name="aff_contact")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\ContactRepository")
 */
class Contacts
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     * @Groups({"contacts:read","contacts:write"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ipcontact;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $loginsource = [];

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $validatetop=false;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $emailCanonical;

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
     * @ORM\OneToOne(targetEntity="App\Entity\Users\ProfilUser", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $useridentity;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DispatchSpace\Tballmessage", mappedBy="contact")
     * @ORM\JoinColumn(nullable=true)
     */
    private $allmessages;


    public function __construct()
    {
        $this->create_at=new DateTimeImmutable();
        $this->allmessages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getEmailCanonical(): ?string
    {
        return $this->emailCanonical;
    }

    public function setEmailCanonical(string $emailCanonical): self
    {
        $this->emailCanonical = $emailCanonical;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->create_at;
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

    public function getUseridentity(): ?ProfilUser
    {
        return $this->useridentity;
    }

    public function setUseridentity(ProfilUser $useridentity): self
    {
        $this->useridentity = $useridentity;

        return $this;
    }

    public function getIpcontact(): ?string
    {
        return $this->ipcontact;
    }

    public function setIpcontact(?string $ipcontact): self
    {
        $this->ipcontact = $ipcontact;

        return $this;
    }

    public function getLoginsource(): ?array
    {
        return $this->loginsource;
    }

    public function setLoginsource(?array $loginsource): self
    {
        $this->loginsource = $loginsource;

        return $this;
    }

    public function addLoginsource($loginsource)
    {
        $loginsource = strtoupper($loginsource);

        if (!in_array($loginsource, $this->loginsource, true)) {
            $this->loginsource[] = $loginsource;
        }

        return $this;
    }

    public function getValidatetop(): ?bool
    {
        return $this->validatetop;
    }

    public function setValidatetop(bool $validatetop): self
    {
        $this->validatetop = $validatetop;

        return $this;
    }

    public function setCreateAt(\DateTimeInterface $create_at): self
    {
        $this->create_at = $create_at;

        return $this;
    }

    /**
     * @return Collection|Tballmessage[]
     */
    public function getAllmessages(): Collection
    {
        return $this->allmessages;
    }

    public function addAllmessage(Tballmessage $allmessage): self
    {
        if (!$this->allmessages->contains($allmessage)) {
            $this->allmessages[] = $allmessage;
            $allmessage->setContact($this);
        }

        return $this;
    }

    public function removeAllmessage(Tballmessage $allmessage): self
    {
        if ($this->allmessages->removeElement($allmessage)) {
            // set the owning side to null (unless already changed)
            if ($allmessage->getContact() === $this) {
                $allmessage->setContact(null);
            }
        }

        return $this;
    }
}