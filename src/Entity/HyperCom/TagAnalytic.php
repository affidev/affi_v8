<?php


namespace App\Entity\HyperCom;

use App\Entity\DispatchSpace\DispatchSpaceWeb;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_taganalytic")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\TagAnalyticRepository")
 */
class TagAnalytic
{
    /**
    * @ORM\Id()
    * @ORM\GeneratedValue(strategy="IDENTITY")
    * @ORM\Column(type="integer")
    */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="\App\Entity\DispatchSpace\DispatchSpaceWeb", mappedBy="analityc")
     * @ORM\JoinColumn(nullable=true)
     */
    private $dispatch;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $tabgps = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $tabcat = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $tablikewebsite = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $sessions;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $log;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTabgps(): ?array
    {
        return $this->tabgps;
    }

    public function setTabgps(?array $tabgps): self
    {
        $this->tabgps = $tabgps;

        return $this;
    }

    public function getTabcat(): ?array
    {
        return $this->tabcat;
    }

    public function setTabcat(?array $tabcat): self
    {
        $this->tabcat = $tabcat;

        return $this;
    }

    public function getTablikewebsite(): ?array
    {
        return $this->tablikewebsite;
    }

    public function setTablikewebsite(?array $tablikewebsite): self
    {
        $this->tablikewebsite = $tablikewebsite;

        return $this;
    }

    public function getSessions(): ?array
    {
        return $this->sessions;
    }

    public function setSessions(?array $sessions): self
    {
        $this->sessions = $sessions;

        return $this;
    }

    public function getLog(): ?string
    {
        return $this->log;
    }

    public function setLog(?string $log): self
    {
        $this->log = $log;

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