<?php


namespace App\Entity\Module;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Websites\Website;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_moduleliste")
*  @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="App\Repository\Entity\ModuleListRepository")
 *  * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"module_list_post:read"}}
 *     )
 */
class ModuleList
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $classmodule;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $keymodule;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Websites\Website", inversedBy="listmodules")
     * @ORM\JoinColumn(nullable=true)
     */
    private $website;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datemaj_at;

    public function __construct()
    {
        $this->create_at = new \DateTimeImmutable();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClassmodule(): ?string
    {
        return $this->classmodule;
    }

    public function setClassmodule(string $classmodule): self
    {
        $this->classmodule = $classmodule;

        return $this;
    }

    public function getKeymodule(): ?string
    {
        return $this->keymodule;
    }

    public function setKeymodule(string $keymodule): self
    {
        $this->keymodule = $keymodule;

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

    public function getWebsite(): ?Website
    {
        return $this->website;
    }

    public function setWebsite(?Website $website): self
    {
        $this->website = $website;

        return $this;
    }

}