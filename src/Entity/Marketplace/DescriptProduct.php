<?php

namespace App\Entity\Marketplace;


use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="aff_descriptproduct")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"descrpt_product:read"}}
 *)
 */
class DescriptProduct
{
    private $file;
    private $data;
    private $tempFilename;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $extension;

    /**
     * @ORM\Column(type="string", length=255,  nullable=true)
     */
    private $fileblob;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tag;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datecreat;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datemodif;

    /**
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted;

    /**
     * @Groups({"descrpt_product:read","product_offre:read","offres_post:read","parutions:read"})
     */
    private $apifileblob;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Marketplace\Noticeproducts")
     */
    private $offre;


    public function setFile($options)
    {
        $this->file = $options['filesource'];
        $this->tempinfo = $options['name'];
        $this->tag = $options['tag'];

        if (null !== $this->fileblob) {
            $this->tempFilename = $this->fileblob;
            $this->extension = null;
            $this->tag = null;

        }
    }

    public function initNameFile()
    {
        if (null === $this->file) return false;
        $this->extension = 'txt';
        $uploadName = sha1(uniqid(mt_rand(), true));
        $this->fileblob=$uploadName.'.'.$this->extension;
        $data = trim($this->file);
        $this->data=nl2br($data);
        if (preg_match("#<script#i", $data)) {// todo supprimer les balises <script> fonction eval et les echos, requete ...
            return false;
        }
        return $this->data;
    }


    public function uploadContent()
    {
        return file_put_contents($this->getUploadRootDir().'/'.$this->fileblob, $this->data);
    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        $this->tempFilename = $this->getUploadRootDir() . '/' . $this->fileblob;
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

    public function deleteFile(){
        if($this->fileblob){
            $this->tempFilename = $this->getUploadRootDir().'/'.$this->fileblob;
            if (file_exists($this->tempFilename)) {
                unlink($this->tempFilename);
            }
            $this->fileblob=null;
        }
    }

    public function getUploadDir()
    {
        return '5764xs4m/blobtxt8_4';
    }

    public function getUploadRootDir()
    {
        return __DIR__ . '/../../../public/' . $this->getUploadDir();
    }

    public function getWebPathblob()
    {
        return $this->getUploadDir() . '/' . $this->fileblob;
    }

    public function getphpPathblob()
    {
        return $this->getUploadRootDir() . '/' . $this->fileblob;
    }

    public function getFileblob(): string
    {
        if ($this->fileblob) {
            return  $this->fileblob;
        } else {
            return false;
        }
    }

    public function getApiFileblob(): string
    {
        if($this->fileblob){
            //return  $this->fileblob;
            return 'https://affichange.com/'.$this->getUploadDir().'/'.$this->fileblob;
        }else{
            return false;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatecreat(): ?\DateTimeInterface
    {
        return $this->datecreat;
    }

    public function setDatecreat(\DateTimeInterface $datecreat): self
    {
        $this->datecreat = $datecreat;

        return $this;
    }

    public function getDatemodif(): ?\DateTimeInterface
    {
        return $this->datemodif;
    }

    public function setDatemodif(?\DateTimeInterface $datemodif): self
    {
        $this->datemodif = $datemodif;

        return $this;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(?bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function setFileblob(?string $fileblob): self
    {
        $this->fileblob = $fileblob;

        return $this;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(?string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getOffre(): ?Noticeproducts
    {
        return $this->offre;
    }

    public function setOffre(?Noticeproducts $offre): self
    {
        $this->offre = $offre;

        // set (or unset) the owning side of the relation if necessary
        $newHtmlcontent = null === $offre ? null : $this;
        if ($offre->getHtmlcontent() !== $newHtmlcontent) {
            $offre->setHtmlcontent($newHtmlcontent);
        }
        return $this;
    }

}
