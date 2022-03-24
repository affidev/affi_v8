<?php


namespace App\Entity\Admin;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="aff_numeratum")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\NumeratumRepository")
 * @UniqueEntity(
 *     fields={"numFact","numCmd", "numClient"}
 *     )
 */
class Numeratum
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $numCmd;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $numFact;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $numClient;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numWebsite;

    public function __construct($init)
    {
        $this->numCmd=$init;
        $this->numFact=$init;
        $this->numClient=$init;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumCmd(): ?int
    {
        return $this->numCmd;
    }

    public function setNumCmd(int $numCmd): self
    {
        $this->numCmd = $numCmd;

        return $this;
    }

    public function getNumFact(): ?int
    {
        return $this->numFact;
    }

    public function setNumFact(int $numFact): self
    {
        $this->numFact = $numFact;

        return $this;
    }

    public function getNumClient(): ?int
    {
        return $this->numClient;
    }

    public function setNumClient(int $numClient): self
    {
        $this->numClient = $numClient;

        return $this;
    }

    public function getNumWebsite(): ?int
    {
        return $this->numWebsite;
    }

    public function setNumWebsite(?int $numWebsite): self
    {
        $this->numWebsite = $numWebsite;

        return $this;
    }
}