<?php


namespace App\Entity\Media;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="aff_background")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Background
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $namefile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alt;


    public function getUploadDir(): string
    {
        return '/spaceweb/template';
    }

    public function getUploadRootDir(): string
    {
        // On retourne le chemin relatif vers l'image pour notre code PHP

        return  __DIR__.'/../../../public/'.$this->getUploadDir();
    }

    public function getWebPath(): string
    {
        return $this->getUploadDir().'/'.$this->namefile;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNamefile(): ?string
    {
        return $this->namefile;
    }

    public function setNamefile(string $namefile): self
    {
        $this->namefile = $namefile;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }
}