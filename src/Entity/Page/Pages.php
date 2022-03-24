<?php

namespace App\Entity\Page;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Constraints as CustomAssert;

/**
 * Pages
 *
 * @ORM\Table(name="pages")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\PagesRepository")
 *
 */
class Pages
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;


    /**
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @ORM\Column(name="slug", type="string", length=180, unique=true)
     */
    private $slug;


    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=180)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text")
     * @CustomAssert\contraintsCheckUrl()
     */
    private $contenu;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set titre
     *
     * @param string $titre
     *
     * @return Pages
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     *
     * @return Pages
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }
    

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Pages
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Pages
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

}
