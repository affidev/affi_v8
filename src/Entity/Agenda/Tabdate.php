<?php


namespace App\Entity\Agenda;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Table(name="aff_tabdate")
 * @ORM\Entity()
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"tabdateeventappoint:read"}}
 *)
 */
class Tabdate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $tabdatestr;

    /**
     * @Groups({"edit_event"})
     * @ORM\Column(type="json", nullable=true)
     */
    private $tabdatejso;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTabdatestr(): ?string
    {
        return $this->tabdatestr;
    }

    public function setTabdatestr(string $tabdatestr): self
    {
        $this->tabdatestr = $tabdatestr;

        return $this;
    }

    public function getTabdatejso(): ?array
    {
        return $this->tabdatejso;
    }

    public function setTabdatejso(?array $tabdatejso): self
    {
        $this->tabdatejso = $tabdatejso;

        return $this;
    }


}