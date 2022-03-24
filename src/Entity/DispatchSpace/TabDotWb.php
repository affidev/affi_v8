<?php


namespace App\Entity\DispatchSpace;

use App\Entity\Websites\Website;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_tabdotwb")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\TabDotWbRepository")
 */
class TabDotWb
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Websites\Website")
     * @ORM\JoinColumn(nullable=false)
     */
    private $website;

    public function  __toString()
    {
        return $this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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