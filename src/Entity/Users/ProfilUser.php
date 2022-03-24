<?php


namespace App\Entity\Users;

use App\Entity\Media\Avatar;
use App\Entity\Sector\Sectors;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Table(name="aff_profiluser")
 * @ORM\Entity()
 */
class ProfilUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Media\Avatar", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $avatar;

    /**
     * @var string $firstname
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $firstname;

    /**
     * @var string $lastname
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $lastname;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sector\Sectors", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $sector;

    /**
     * @var datetime $birthdate
     *
     * @Assert\Type("\DateTimeInterface")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $birthdate;

    /**
     * @var string $job
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $job;

    /**
     * @var string $sex
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $sex;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mdpfirst;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $emailfirst;

    /**
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     *
     * @ORM\Column(type="string",  length=255, nullable=true)
     */
    private $emailsecours;

    /**
     * @ORM\Column(type="string", length=35, nullable=true)
     */
    private $telephonefixe;

    /**
     * @ORM\Column(type="string", length=35, nullable=true)
     */
    private $telephonemobile;


    public function setBirthdate($birthdate) {

        if($birthdate != null) {
            $this->birthdate =  $birthdate;  //\DateTime::createFromFormat('d/m/Y',
        }
        else {
            $this->birthdate = null;
        }

        return $this;
    }

    public function __toString() {
        return '#'.$this->id.' - '.$this->firstname.' '.$this->lastname;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(?string $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(?string $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    public function getMdpfirst(): ?string
    {
        return $this->mdpfirst;
    }

    public function setMdpfirst(?string $mdpfirst): self
    {
        $this->mdpfirst = $mdpfirst;

        return $this;
    }

    public function getEmailsecours(): ?string
    {
        return $this->emailsecours;
    }

    public function setEmailsecours(?string $emailsecours): self
    {
        $this->emailsecours = $emailsecours;

        return $this;
    }

    public function getTelephonefixe()
    {
        return $this->telephonefixe;
    }

    public function setTelephonefixe($telephonefixe): self
    {
        $this->telephonefixe = $telephonefixe;

        return $this;
    }

    public function getTelephonemobile()
    {
        return $this->telephonemobile;
    }

    public function setTelephonemobile($telephonemobile): self
    {
        $this->telephonemobile = $telephonemobile;

        return $this;
    }

    public function getEmailfirst(): ?string
    {
        return $this->emailfirst;
    }

    public function setEmailfirst(?string $emailfirst): self
    {
        $this->emailfirst = $emailfirst;

        return $this;
    }

    public function getSector(): ?Sectors
    {
        return $this->sector;
    }

    public function setSector(?Sectors $sector): self
    {
        $this->sector = $sector;

        return $this;
    }

    public function getAvatar(): ?Avatar
    {
        return $this->avatar;
    }

    public function setAvatar(?Avatar $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }
}