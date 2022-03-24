<?php


namespace App\Entity\UserMap;


use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\Marketplace\Noticeproducts;
use App\Entity\Module\PostEvent;
use App\Entity\Bulles\Bulle;
use App\Entity\Posts\Post;
use App\Entity\Websites\Template;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Table(name="aff_taguery")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\TagueryRepository")
 * @UniqueEntity("namewebsite")
 * @ApiResource(
 * collectionOperations={"get"},
 * itemOperations={"get"},
 * normalizationContext={"groups"={"tag_post:read"}}
 *)
 */
class Taguery
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Bulles\Bulle", mappedBy="tagueries")
     * @ORM\JoinColumn(nullable=true)
     */
    private $bulles;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Module\PostEvent", mappedBy="tagueries")
     * @ORM\JoinColumn(nullable=true)
     */
    private $postevents;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\DispatchSpace\DispatchSpaceWeb", mappedBy="tagueries")
     * @ORM\JoinColumn(nullable=true)
     */
    private $dispatch;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Websites\Template", mappedBy="tagueries")
     * @ORM\JoinColumn(nullable=true)
     */
    private $template;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Posts\Post", mappedBy="tagueries")
     * @ORM\JoinColumn(nullable=true)
     */
    private $postations;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Marketplace\Noticeproducts", mappedBy="tagueries")
     * @ORM\JoinColumn(nullable=true)
     */
    private $noticeproducts;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\UserMap\Tagcat", mappedBy="tagueries")
     * @ORM\JoinColumn(nullable=true)
     */
    private $catag;

    /**
     * @Groups({"buble"})
     * @Groups({"tag_post:read","postation_post:read","module_post:read","website_post:read"})
     * @ORM\Column(type="string", length=125, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $associatekey;

    /**
     * @var float
     *
     * @ORM\Column(type="string", length=125, nullable=false)
     */
    private $phylo;

    public function __construct()
    {
        $this->bulles = new ArrayCollection();
        $this->postevents = new ArrayCollection();
        $this->postations = new ArrayCollection();
        $this->dispatch = new ArrayCollection();
        $this->template = new ArrayCollection();
        $this->noticeproducts = new ArrayCollection();
        $this->catag = new ArrayCollection();
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

    public function getAssociatekey(): ?string
    {
        return $this->associatekey;
    }

    public function setAssociatekey(?string $associatekey): self
    {
        $this->associatekey = $associatekey;

        return $this;
    }

    public function getPhylo(): ?string
    {
        return $this->phylo;
    }

    public function setPhylo(string $phylo): self
    {
        $this->phylo = $phylo;

        return $this;
    }

    /**
     * @return Collection|Bulle[]
     */
    public function getBulles(): Collection
    {
        return $this->bulles;
    }

    public function addBulle(Bulle $bulle): self
    {
        if (!$this->bulles->contains($bulle)) {
            $this->bulles[] = $bulle;
            $bulle->addTaguery($this);
        }

        return $this;
    }

    public function removeBulle(Bulle $bulle): self
    {
        if ($this->bulles->contains($bulle)) {
            $this->bulles->removeElement($bulle);
            $bulle->removeTaguery($this);
        }

        return $this;
    }

    /**
     * @return Collection|PostEvent[]
     */
    public function getPostevents(): Collection
    {
        return $this->postevents;
    }

    public function addPostevent(PostEvent $postevent): self
    {
        if (!$this->postevents->contains($postevent)) {
            $this->postevents[] = $postevent;
            $postevent->addTaguery($this);
        }

        return $this;
    }

    public function removePostevent(PostEvent $postevent): self
    {
        if ($this->postevents->contains($postevent)) {
            $this->postevents->removeElement($postevent);
            $postevent->removeTaguery($this);
        }

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPostations(): Collection
    {
        return $this->postations;
    }

    public function addPostation(Post $postation): self
    {
        if (!$this->postations->contains($postation)) {
            $this->postations[] = $postation;
            $postation->addTaguery($this);
        }

        return $this;
    }

    public function removePostation(Post $postation): self
    {
        if ($this->postations->contains($postation)) {
            $this->postations->removeElement($postation);
            $postation->removeTaguery($this);
        }

        return $this;
    }

    /**
     * @return Collection|DispatchSpaceWeb[]
     */
    public function getDispatch(): Collection
    {
        return $this->dispatch;
    }

    public function addDispatch(DispatchSpaceWeb $dispatch): self
    {
        if (!$this->dispatch->contains($dispatch)) {
            $this->dispatch[] = $dispatch;
            $dispatch->addTaguery($this);
        }

        return $this;
    }

    public function removeDispatch(DispatchSpaceWeb $dispatch): self
    {
        if ($this->dispatch->contains($dispatch)) {
            $this->dispatch->removeElement($dispatch);
            $dispatch->removeTaguery($this);
        }

        return $this;
    }

    /**
     * @return Collection|Template[]
     */
    public function getTemplate(): Collection
    {
        return $this->template;
    }

    public function addTemplate(Template $template): self
    {
        if (!$this->template->contains($template)) {
            $this->template[] = $template;
            $template->addTaguery($this);
        }

        return $this;
    }

    public function removeTemplate(Template $template): self
    {
        if ($this->template->contains($template)) {
            $this->template->removeElement($template);
            $template->removeTaguery($this);
        }

        return $this;
    }

    /**
     * @return Collection|Noticeproducts[]
     */
    public function getNoticeproducts(): Collection
    {
        return $this->noticeproducts;
    }

    public function addNoticeproduct(Noticeproducts $noticeproduct): self
    {
        if (!$this->noticeproducts->contains($noticeproduct)) {
            $this->noticeproducts[] = $noticeproduct;
            $noticeproduct->addTaguery($this);
        }

        return $this;
    }

    public function removeNoticeproduct(Noticeproducts $noticeproduct): self
    {
        if ($this->noticeproducts->contains($noticeproduct)) {
            $this->noticeproducts->removeElement($noticeproduct);
            $noticeproduct->removeTaguery($this);
        }

        return $this;
    }

    /**
     * @return Collection|Tagcat[]
     */
    public function getCatag(): Collection
    {
        return $this->catag;
    }

    public function addCatag(Tagcat $catag): self
    {
        if (!$this->catag->contains($catag)) {
            $this->catag[] = $catag;
            $catag->addTaguery($this);
        }

        return $this;
    }

    public function removeCatag(Tagcat $catag): self
    {
        if ($this->catag->removeElement($catag)) {
            $catag->removeTaguery($this);
        }

        return $this;
    }
}