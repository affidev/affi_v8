<?php

namespace App\Entity\Media;


use Doctrine\ORM\Mapping as ORM;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
* @ORM\Table(name="aff_imagesjpg")
* @ORM\Entity
* @ORM\HasLifecycleCallbacks
* @ApiResource(
*     collectionOperations={"get"},
*     itemOperations={"get"},
*     normalizationContext={"groups"={"img_post:read"}}
*)
*/

class Imagejpg

{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
    * @ORM\ManyToOne(targetEntity="App\Entity\Media\Media", inversedBy="imagejpg")
    * @ORM\JoinColumn(nullable=true))
    */
    private $idmedia;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $extension;

    /**
     * @Groups({"simplebuble","edit_event","event_post:read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $namefile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $namethumb;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alt;
    /** @var UploadedFile file */
    private $file;
    private $tempFilename;
    private $typefile;
    private $ext;


   
    public function setFile($options)
    {
          $this->file = $options['file'];
          $this->alt = $options['name'];
          $this->typefile = $options['filetyp']; //soit 'file' soit '64'
          if (null !== $this->namefile) {
              $this->tempFilename = $this->namefile;
              $this->ext = null;
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

            if ($this->typefile === "file") {
                $this->extension = $this->file->guessExtension();
                $uploadName = sha1(uniqid(mt_rand(), true));
                $this->namefile = $uploadName . '.' . $this->extension;
            } else {
                $this->extension = '64';
                //$namefile=substr($this->tempinfo, strrpos($this->tempinfo,"."));  //todo le nom de l'image et lee alt
                //$this->alt = $namefile;
                $uploadName = sha1(uniqid(mt_rand(), true));
                $this->namefile = $uploadName . '.' . 'jpg';
            }

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

        if(null !== $this->tempFilename){
            $oldfile=$this->getUploadRootDir().'/'.$this->tempFilename;
            if(file_exists($oldfile)){
                unlink($oldfile);
            }
        }

            if ($this->typefile === "file") {
                  $manager = new ImageManager();
                  $manager->make($this->file)
                      ->fit(450, null)
                      ->save($this->getUploadRootDir() . '/' . $this->namefile)
                      ->destroy();
              } else {
                  $parts = explode(',', $this->file);
                  $data = $parts[1];
                  $data = base64_decode($data);
                  file_put_contents($this->getUploadRootDir() . '/' . $this->namefile, $data);
              }


      }

      /**
       * @ORM\PreRemove()
       */
      public function preRemoveUpload()
      {
        $this->tempFilename = $this->getUploadRootDir().'/'.$this->namefile;
      }

      /**
       * @ORM\PostRemove()
       */
      public function removeUpload()
      {
        if (file_exists($this->tempFilename)) {
          unlink($this->tempFilename);
        }
      }

      public function getUploadDir()
      {
        return 'upload/module';
      }


      public function getUploadRootDir()
      {
        // On retourne le chemin relatif vers l'image pour notre code PHP

        return  __DIR__.'/../../../public/'.$this->getUploadDir();

      }

      public function getWebPath()
      {
        return $this->getUploadDir().'/'.$this->namefile;
      }

      /**
       * @Groups({"img_post:read","offres_post:read","media_post:read","postation_post:read","module_post:read","website_post:read"})
      */
      public function getApiPath()
      {
        return 'https://affichange.com/'.$this->getUploadDir().'/'.$this->namefile;
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

      public function getFile()
        {
        return $this->file;
        }

        public function getIdmedia(): ?Media
       {
           return $this->idmedia;
       }

       public function setIdmedia(?Media $idmedia): self
       {
           $this->idmedia = $idmedia;

           return $this;
       }

       public function getNamethumb(): ?string
       {
           return $this->namethumb;
       }

       public function setNamethumb(?string $namethumb): self
       {
           $this->namethumb = $namethumb;

           return $this;
       }

            
}
