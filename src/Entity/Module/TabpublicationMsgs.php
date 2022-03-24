<?php


namespace App\Entity\Module;


use App\Entity\LogMessages\PublicationConvers;
use App\Entity\MarketPlace\Offres;
use App\Entity\Posts\Post;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_tabpublicationMsgs")
 * @ORM\Entity()
 */
class TabpublicationMsgs
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\OneToMany (targetEntity="App\Entity\LogMessages\PublicationConvers", mappedBy="tabpublication")
     */
    private $idmessage;

    /**
     * @ORM\OneToOne(targetEntity="\App\Entity\Posts\Post", inversedBy="tbmessages")
     */
    private $post;

    /**
     * @ORM\OneToOne(targetEntity="\App\Entity\Marketplace\Offres", inversedBy="tbmessages")
     */
    private $offre;

    public function __construct()
    {
        $this->idmessage = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getOffre(): ?Offres
    {
        return $this->offre;
    }

    public function setOffre(?Offres $offre): self
    {
        $this->offre = $offre;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getIdmessage(): Collection
    {
        return $this->idmessage;
    }

    public function addIdmessage(PublicationConvers $idmessage): self
    {
        if (!$this->idmessage->contains($idmessage)) {
            $this->idmessage[] = $idmessage;
            $idmessage->setTabpublication($this);
        }

        return $this;
    }

    public function removeIdmessage(PublicationConvers $idmessage): self
    {
        if ($this->idmessage->removeElement($idmessage)) {
            // set the owning side to null (unless already changed)
            if ($idmessage->getTabpublication() === $this) {
                $idmessage->setTabpublication(null);
            }
        }

        return $this;
    }

}