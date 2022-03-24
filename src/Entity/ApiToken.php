<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="aff_apptoken")
 * @ORM\Entity(repositoryClass="App\Repository\ApiTokenRepository")
 */
class ApiToken
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expireAt;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct(User $user)
    {
        $this->token=bin2hex(random_bytes(60));
        $this->user=$user;
        $this->expireAt=new DateTime('+1 hour');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getExpireAt(): ?DateTimeInterface
    {
        return $this->expireAt;
    }

    public function setExpireAt(DateTimeInterface $expireAt): self
    {
        $this->expireAt = $expireAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function renewExpireAt()
    {
        $this->expireAt=new DateTime('+1 hour');
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

}
