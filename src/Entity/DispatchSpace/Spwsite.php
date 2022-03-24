<?php


namespace App\Entity\DispatchSpace;

use App\Entity\Posts\Post;
use App\Entity\Websites\Website;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="aff_spwsite")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\SpwsiteRepository")
 * @UniqueEntity("token")
 */
class Spwsite
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DispatchSpace\DispatchSpaceWeb", inversedBy="spwsite")
     */
    private $disptachwebsite;

    /**
     * @ORM\ManyToMany(targetEntity="\App\Entity\Posts\Post", mappedBy="members")
     * @ORM\JoinColumn(nullable=true)
     */
    private $postmember;

    /**
     * @ORM\Column(type="json")
     */
    private $termes = [];

    /**
     * @ORM\Column(type="string", length=255,  nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $role;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $isadmin = false;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $isdefault = false;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Websites\Website", inversedBy="spwsites", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $website;

    public function __construct()
    {
        $this->create_at=new DateTime();
        $this->postmember = new ArrayCollection();
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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function isSuper(): ?string
    {
        return $this->role==="superadmin";
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

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

    public function getDisptachwebsite(): ?DispatchSpaceWeb
    {
        return $this->disptachwebsite;
    }

    public function setDisptachwebsite(?DispatchSpaceWeb $disptachwebsite): self
    {
        $this->disptachwebsite = $disptachwebsite;

        return $this;
    }

    public function getWebsite(): ?Website
    {
        return $this->website;
    }

    public function setWebsite(?Website $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function isAdmin(): ?bool
    {
        return $this->isadmin;
    }

    public function activeAdmin(): self
    {
        $this->isadmin = true;

        return $this;
    }

    public function getIsadmin(): ?bool
    {
        return $this->isadmin;
    }

    public function setIsadmin(bool $isadmin): self
    {
        $this->isadmin = $isadmin;

        return $this;
    }

    public function getTermes(): ?array
    {
        return $this->termes;
    }

    public function setTermes(array $termes): self
    {
        $this->termes = $termes;

        return $this;
    }

    public function isDefault(): ?bool
    {
        return $this->isdefault;
    }

    public function getIsdefault(): ?bool
    {
        return $this->isdefault;
    }

    public function setIsdefault(bool $isdefault): self
    {
        $this->isdefault = $isdefault;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPostmember(): Collection
    {
        return $this->postmember;
    }

    public function addPostmember(Post $postmember): self
    {
        if (!$this->postmember->contains($postmember)) {
            $this->postmember[] = $postmember;
            $postmember->addMember($this);
        }

        return $this;
    }

    public function removePostmember(Post $postmember): self
    {
        if ($this->postmember->removeElement($postmember)) {
            $postmember->removeMember($this);
        }

        return $this;
    }

}