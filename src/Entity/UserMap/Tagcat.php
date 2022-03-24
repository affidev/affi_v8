<?php


namespace App\Entity\UserMap;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Table(name="aff_tagcat")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\TagcatRepository")
 * @UniqueEntity("namewebsite")
 */
class Tagcat
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=125, nullable=false)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\UserMap\Taguery", inversedBy="catag")
     * @ORM\JoinColumn(nullable=true)
     */
    private $tagueries;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserMap\Hits", inversedBy="catag")
     * @ORM\JoinColumn(nullable=true)
     */
    private $hits;

    /**
     * @Groups({"buble"})
     * @ORM\Column(type="integer", nullable=false, options={"default":0})
     */
    private $score=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=false, options={"default":0})
     */
    private $ponderation=0;

    public function __construct()
    {
        $this->tagueries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getPonderation(): ?float
    {
        return $this->ponderation;
    }

    public function setPonderation(float $ponderation): self
    {
        $this->ponderation = $ponderation;

        return $this;
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

    public function getHits(): ?Hits
    {
        return $this->hits;
    }

    public function setHits(?Hits $hits): self
    {
        $this->hits = $hits;

        return $this;
    }

    /**
     * @return Collection|Taguery[]
     */
    public function getTagueries(): Collection
    {
        return $this->tagueries;
    }

    public function addTaguery(Taguery $taguery): self
    {
        if (!$this->tagueries->contains($taguery)) {
            $this->tagueries[] = $taguery;
        }

        return $this;
    }

    public function removeTaguery(Taguery $taguery): self
    {
        $this->tagueries->removeElement($taguery);

        return $this;
    }


}