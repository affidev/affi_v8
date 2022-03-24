<?php

namespace App\Entity\Websites;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Admin\Wbcustomers;
use App\Entity\DispatchSpace\Spwsite;
use App\Entity\LogMessages\MsgWebsite;
use App\Entity\Module\Contactation;
use App\Entity\Module\ModuleList;
use App\Entity\Module\PostEvent;
use App\Entity\Sector\Gps;
use App\Entity\UserMap\Hits;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\String\Slugger\SluggerInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;


/**
 * @ORM\Table(name="aff_websites")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\WebsiteRepository")
 * @UniqueEntity(
 *     fields={"codesite","namewebsite","slug"},
 *     errorPath="namewebsite",
 *     message="Ce nom est délà utlisé..."
 * )
 * @ApiResource(
 *     collectionOperations={
 *      "get"={"path"="/ressources/affi/website/search/allfind/"}},
 *     itemOperations={
 *      "get"={"path"="ressources/affi/website/search/find/{id}"}},
 *     normalizationContext={"groups"={"website_post:read", "website_adress","website_partner"}}
 *     )
 * @ApiFilter(SearchFilter::class, properties={"codesite": "exact"})
 */

class Website
{
    /**
     * @Groups({"website_post:read", "website_adress"})
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     * @Groups({"edit_event"})
     */
    private $id;

    /**
     * @Assert\Length(
     * min=3,
     * max=100,
     * minMessage="le nom doit faire au moins {{ limit }} caractères",
     * maxMessage="le nom doit faire au maximum {{ limit }} caractères")
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=100, unique=true)
     * @Groups({"website_post:read","website_adress","edit_event"})
     */
    private $namewebsite;

    /**
     * *@Assert\Url(
     *     message = "l'url '{{ value }}' n'est pas valide."
     * )
     *
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $statut=false;

    /**
     * @Groups({"website_post:read","website_adress"})
     * @ORM\Column(type="string", length=255,  nullable=true, unique=true)
     */
    private $codesite;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Admin\Wbcustomers", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $wbcustomer;

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
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $attached = true;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Websites\Opendays", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $tabopendays;

    /**
     * @Groups({"website_adress", "edit_event"})
     * @ORM\OneToOne(targetEntity="App\Entity\Websites\Template", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $template;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Module\ModuleList", mappedBy="website",cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $listmodules;


    /**
    * @ORM\OneToOne(targetEntity="App\Entity\Module\Contactation", mappedBy="website", cascade={"persist","remove"})
    * @ORM\JoinColumn(nullable=true)
     */
    private $contactation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DispatchSpace\Spwsite", mappedBy="website")
     * @ORM\JoinColumn(nullable=true)
     */
    private $spwsites;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sector\Gps")
     * @ORM\JoinColumn(nullable=true))
     */
    private $locality;

    /**
     * @ORM\Column(type="string", length=190, unique=true)
     * @Groups({"website_post:read"})
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LogMessages\MsgWebsite", mappedBy="websitedest", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $msgs;

    /**
     * @Groups({"website_partner"})
     * @ORM\ManyToMany (targetEntity="App\Entity\Websites\Website")
     * @ORM\JoinColumn(nullable=true)
     */
    private $websitepartner;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Module\PostEvent", inversedBy="partners")
     * @ORM\JoinColumn(nullable=true)
     */
    private $eventpartner;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserMap\Hits", mappedBy="website")
     * @ORM\JoinColumn(nullable=true)
     */
    private $hits;


    public function __construct()
    {
        $this->create_at=new DateTime();
        $this->listmodules = new ArrayCollection();
        $this->spwsites = new ArrayCollection();
        $this->msgs = new ArrayCollection();
        $this->websitepartner = new ArrayCollection();
        $this->eventpartner = new ArrayCollection();
    }

    public function  __toString(): string
    {
        return $this->namewebsite;
    }

    public function websiteSlug(SluggerInterface $slugger)
    {
        $this->slug = (string) $slugger->slug((string) $this)->lower();
        /*
        if (!$this->slug || '-' === $this->slug) {
            $this->slug = (string) $slugger->slug((string) $this)->lower();
        }
        */
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNamewebsite(): ?string
    {
        return $this->namewebsite;
    }

    public function setNamewebsite(string $namewebsite): self
    {
        $this->namewebsite = $namewebsite;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getCodesite(): ?string
    {
        return $this->codesite;
    }

    public function setCodesite(?string $codesite): self
    {
        $this->codesite = $codesite;

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

    public function getAttached(): ?bool
    {
        return $this->attached;
    }

    public function setAttached(bool $attached): self
    {
        $this->attached = $attached;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getWbcustomer(): ?Wbcustomers
    {
        return $this->wbcustomer;
    }

    public function setWbcustomer(?Wbcustomers $wbcustomer): self
    {
        $this->wbcustomer = $wbcustomer;

        return $this;
    }

    public function getTabopendays(): ?Opendays
    {
        return $this->tabopendays;
    }

    public function setTabopendays(?Opendays $tabopendays): self
    {
        $this->tabopendays = $tabopendays;

        return $this;
    }

    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    public function setTemplate(?Template $template): self
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return Collection|ModuleList[]
     */
    public function getListmodules(): Collection
    {
        return $this->listmodules;
    }

    public function addListmodule(ModuleList $listmodule): self
    {
        if (!$this->listmodules->contains($listmodule)) {
            $this->listmodules[] = $listmodule;
            $listmodule->setWebsite($this);
        }

        return $this;
    }

    public function removeListmodule(ModuleList $listmodule): self
    {
        if ($this->listmodules->removeElement($listmodule)) {
            // set the owning side to null (unless already changed)
            if ($listmodule->getWebsite() === $this) {
                $listmodule->setWebsite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Spwsite[]
     */
    public function getSpwsites(): Collection
    {
        return $this->spwsites;
    }

    public function addSpwsite(Spwsite $spwsite): self
    {
        if (!$this->spwsites->contains($spwsite)) {
            $this->spwsites[] = $spwsite;
            $spwsite->setWebsite($this);
        }

        return $this;
    }

    public function removeSpwsite(Spwsite $spwsite): self
    {
        if ($this->spwsites->removeElement($spwsite)) {
            // set the owning side to null (unless already changed)
            if ($spwsite->getWebsite() === $this) {
                $spwsite->setWebsite(null);
            }
        }

        return $this;
    }

    public function getLocality(): ?Gps
    {
        return $this->locality;
    }

    public function setLocality(?Gps $locality): self
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMsgs(): Collection
    {
        return $this->msgs;
    }

    public function addMsg(MsgWebsite $msg): self
    {
        if (!$this->msgs->contains($msg)) {
            $this->msgs[] = $msg;
            $msg->setWebsitedest($this);
        }

        return $this;
    }

    public function removeMsg(MsgWebsite $msg): self
    {
        if ($this->msgs->removeElement($msg)) {
            // set the owning side to null (unless already changed)
            if ($msg->getWebsitedest() === $this) {
                $msg->setWebsitedest(null);
            }
        }

        return $this;
    }

    public function getContactation(): ?Contactation
    {
        return $this->contactation;
    }

    public function setContactation(?Contactation $contactation): self
    {
        $this->contactation = $contactation;

        return $this;
    }

    /**
     * @return Collection|Website[]
     */
    public function getWebsitepartner(): Collection
    {
        return $this->websitepartner;
    }

    public function addWebsitepartner(Website $websitepartner): self
    {
        if (!$this->websitepartner->contains($websitepartner)) {
            $this->websitepartner[] = $websitepartner;
        }

        return $this;
    }

    public function removeWebsitepartner(Website $websitepartner): self
    {
        $this->websitepartner->removeElement($websitepartner);

        return $this;
    }

    /**
     * @return Collection|PostEvent[]
     */
    public function getEventpartner(): Collection
    {
        return $this->eventpartner;
    }

    public function addEventpartner(PostEvent $eventpartner): self
    {
        if (!$this->eventpartner->contains($eventpartner)) {
            $this->eventpartner[] = $eventpartner;
        }

        return $this;
    }

    public function removeEventpartner(PostEvent $eventpartner): self
    {
        $this->eventpartner->removeElement($eventpartner);

        return $this;
    }

    public function getHits(): ?Hits
    {
        return $this->hits;
    }

    public function setHits(?Hits $hits): self
    {
        $this->hits = $hits;

        return $this;
    }

}