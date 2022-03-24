<?php

namespace App\Entity\Media;


use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Table(name="aff_pdfstore")
* @ORM\Entity
* @ORM\HasLifecycleCallbacks
*/

class Pdfstore
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
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $extension;

    /**
     * @var text
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $alt;
    
    private $file;
    private $temp;


    public function setPdf($pdf)
    {
      $this->file = $pdf;

        if (null !== $this->name) {
          $this->temp = $this->name;
          $this->extension = null;
          $this->alt = null;
        }
      
    }

  /**
   * @ORM\PrePersist()
   * @ORM\PreUpdate()
   */
  public function preUpload()
  {
    if (null === $this->file) {
      return;
    }
    $this->extension = "pdf";
    $this->alt = $this->file['name'];
    $uploadName = sha1(uniqid(mt_rand(), true));
    $this->name=$uploadName.'.'.$this->extension;
    
  }

  /**
   * @ORM\PostPersist()
   * @ORM\PostUpdate()
   */
  public function upload()
  {

    if (null === $this->file) {
      return;
    }

    try
    {
        move_uploaded_file($this->file['tmp_name'],
            $this->getUploadRootDir().'/'.$this->name
        );
    }
    catch (FileException $e)
    {
        echo 'erreur chargement du fichiers';
    }
 
  }

  /**
   * @ORM\PreRemove()
   */
  public function preRemoveUpload()
  {
    $this->$temp = $this->getUploadRootDir().$this->name;
  }

  /**
   * @ORM\PostRemove()
   */
  public function removeUpload()
  {

    if (file_exists($this->temp)) {
      unlink($this->temp);
    }
  }

  public function getUploadDir()
  {
    return 'uploads/storepdf';
  }

  protected function getUploadRootDir()
  {
    // On retourne le chemin relatif vers l'image pour notre code PHP
    
    return  __DIR__.'/../../public/'.$this->getUploadDir();

  }

  public function getPdfPath()
  {
    return $this->getUploadDir().'/'.$this->name;
  }


  public function getId(): ?int
  {
      return $this->id;
  }

  public function getExtension(): ?string
  {
      return $this->extension;
  }

  public function setExtension(string $extension): self
  {
      $this->extension = $extension;

      return $this;
  }

  public function getName(): ?string
  {
      return $this->namelibraryfile;
  }

  public function setName(string $namefile): self
  {
      $this->namelibraryfile = $namelibraryfile;

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
    public function getFile()
    {
    return $this->file;
    }

}