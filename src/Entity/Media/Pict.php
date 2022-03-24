<?php


namespace App\Entity\Media;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 *
 * @ORM\Table("aff_pict")
 * @ORM\Entity(repositoryClass="App\Repository\Entity\PictRepository")
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"pictformule_post:read"}}
 *)
 */
    class Pict
    {
        /**
         * @ORM\Id()
         * @ORM\GeneratedValue(strategy="IDENTITY")
         * @ORM\Column(type="integer")
         */
        private $id;

        /**
         * @Groups({"buble", "edit_event"})
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private $namefile;

        /**
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private $alt;

        private $file;

        public function getFile()
        {
            return $this->file;
        }

        public function setFile(UploadedFile $file = null)
        {
            $this->file = $file;
        }

        public function getUploadDir()
        {
            return 'spaceweb/template';
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
         * @Groups({"pictformule_post:read","artformule_post:read","formules_post:read"})
         */
        public function getApiPath()
        {
            return 'https://affichange.com/'.$this->getUploadDir().'/'.$this->namefile;
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

        public function removeUpload()
        {
            if (file_exists($this->namefile)) {
                unlink($this->getUploadRootDir(). '/' .$this->namefile);
            }
        }

}