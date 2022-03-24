<?php

namespace App\Entity;


use App\Entity\Customer\Customers;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="aff_user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface,\Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Customer\Customers")
     * @ORM\JoinColumn(nullable=true)
     */
    private $customer;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $emailCanonical;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var string
     */
    private $plainPassword;

    /**
     * The salt to use for hashing.
     *
     * @var string
     */
    protected $salt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
    * @ORM\Column(type="boolean", nullable=false, options={"default":true})
    */
    private $enabled=true;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default":false})
     * @Assert\IsTrue(message="Vous devez accepter les CGU pour lancer l'activation de votre SpaceWeb.")
     */
    private $charte=false;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datemaj_at;

    /**
     * Random string sent to the user email address in order to verify it.
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $confirmationToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $passwordRequestedAt;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $active = true;

    /**
     * @ORM\Column(type="string", length=180, unique=true, nullable=true)
     */
    private $apiToken;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->enabled = false;
        $this->roles = array();
        $this->create_at=new DateTime();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getEmailCanonical();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return $this->salt;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === 'ROLE_USER') {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }


    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function serialize()
    {
        return serialize(array(
            $this->password,
            $this->enabled,
            $this->id,
            $this->email,
            $this->emailCanonical,
        ));
    }


    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        if (13 === count($data)) {
            // Unserializing a User object from 1.3.x
            unset($data[4], $data[5], $data[6], $data[9], $data[10]);
            $data = array_values($data);
        } elseif (11 === count($data)) {
            // Unserializing a User from a dev version somewhere between 2.0-alpha3 and 2.0-beta1
            unset($data[4], $data[7], $data[8]);
            $data = array_values($data);
        }

        list(
            $this->password,
            $this->enabled,
            $this->id,
            $this->email,
            $this->emailCanonical
            ) = $data;
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

    public function getEmailCanonical(): ?string
    {
        return $this->emailCanonical;
    }

    public function setEmailCanonical(string $emailCanonical)
    {
        $this->emailCanonical = $emailCanonical;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $time = null): self
    {
        $this->lastLogin = $time;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getPasswordRequestedAt(): ?\DateTimeInterface
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?\DateTimeInterface $date = null)
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
            $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }


    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }


    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }


    public function isAccountNonExpired()
    {
        return true;
    }


    public function isAccountNonLocked()
    {
        return true;
    }


    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(?string $apiToken): self
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getCharte(): ?bool
    {
        return $this->charte;
    }

    public function setCharte(?bool $charte): self
    {
        $this->charte = $charte;

        return $this;
    }

    public function getCustomer(): ?Customers
    {
        return $this->customer;
    }

    public function setCustomer(?Customers $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }
}
