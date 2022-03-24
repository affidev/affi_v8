<?php


namespace App\Entity\Websites;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Table(name="aff_tabopen")
 * @ORM\Entity()
 */
class Opendays
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
    private $tabunique;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $conges;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $tabuniquejso;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $congesjso;

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
        $this->create_at=new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTabunique(): ?string
    {
        return $this->tabunique;
    }

    public function setTabunique(string $tabunique): self
    {
        $this->tabunique = $tabunique;

        return $this;
    }

    public function getConges(): ?string
    {
        return $this->conges;
    }

    public function setConges(?string $conges): self
    {
        $this->conges = $conges;

        return $this;
    }

    public function getTabuniquejso(): ?array
    {
        return $this->tabuniquejso;
    }

    public function setTabuniquejso(?array $tabuniquejso): self
    {
        $this->tabuniquejso = $tabuniquejso;

        return $this;
    }

    public function getCongesjso(): ?array
    {
        return $this->congesjso;
    }

    public function setCongesjso(?array $congesjso): self
    {
        $this->congesjso = $congesjso;

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

}