<?php


namespace App\Module;

use App\Entity\Food\ArticlesFormule;
use App\Entity\Media\Pict;
use App\Lib\MsgAjax;
use Doctrine\ORM\EntityManagerInterface;

class Artfoodator
{
    private EntityManagerInterface $em;

    /**
     * Postar constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param $key
     * @param $form
     * @param $carte ArticlesFormule
     * @return array
     */
    public function newCarte($key, $form, ArticlesFormule $carte): array
    {
        $carte->setKeymodule($key);
        if($basedata=$form->get('base64')->getData()){
            $npict= new Pict();
            list($type, $data) = explode(';', $basedata);
            list(, $data)      = explode(',', $basedata);
            $parts = base64_decode($data);
            $img = imagecreatefromstring($parts);
            if($img) {
                $uploadName = sha1(uniqid(mt_rand(), true));
                $namefile = $uploadName . '.' . 'jpg';
                imagejpeg($img, $npict->getUploadRootDir() . '/' . $namefile);
                $npict->setNamefile($namefile);
                $carte->setPict($npict);
            }
        }
        $this->em->persist($carte);
        $this->em->flush();
        return MsgAjax::MSG_POSTOK;
    }


    /**
     * @param $key
     * @param $form
     * @param ArticlesFormule $carte
     * @return array
     */
    public function editCarte($key, $form, ArticlesFormule $carte): array
    {
        if($basedata=$form->get('base64')->getData()){
            $ipict=$carte->getPict();
            if($ipict->getNamefile()!= null){
                $ipict->removeUpload();
            }
            list($type, $data) = explode(';', $basedata);
            list(, $data)      = explode(',', $basedata);
            $parts = base64_decode($data);
            $img = imagecreatefromstring($parts);
            if($img) {
                $uploadName = sha1(uniqid(mt_rand(), true));
                $namefile = $uploadName . '.' . 'jpg';
                imagejpeg($img, $ipict->getUploadRootDir() . '/' . $namefile);
                $ipict->setNamefile($namefile);
                $carte->setPict($ipict);
            }
        }
        $this->em->persist($carte);
        $this->em->flush();
        return MsgAjax::MSG_POSTOK;
    }


    /**
     * @param ArticlesFormule $carte
     * @return array
     */
    public function removeCarte(ArticlesFormule $carte): array
    {
        $this->em->remove($carte);
        $this->em->flush();
        return MsgAjax::MSG_POSTOK;
    }


    //********************************************   todo *************************************************//

  /*
    public function duplicateCarte($website, Formules $formule) //todo
    {

        $new=clone $formule;
        if($new->getId()) {
            $new->setId(null);
        }
        $new->setPublied(false);
        if($formule->getName()) $new->setName($formule->getName().'- copie');
        $appointment = new Appointments();
        // on instancie la parution(appointements)
        $new->setParution($this->evenator->alongDaysNow($formule, $appointment));
        $new->setCreateAt(new DateTime());
        $articles=$formule->getArticles();
        foreach ($articles as $article){
            $newarticle= clone $article;
            if($newarticle->getId()){
                $newarticle->setId(null);
            }
            $newarticle->setPict($article->getPict());
            $pricearticle=$article->getPrices();
            if($pricearticle){
                $newpricearticle=clone $pricearticle;
                if($newpricearticle->getId()){
                    $newpricearticle->setId(null);
                }
                $newarticle->setPrices($newpricearticle);
            }
            $new->addArticle($newarticle);
        }
        $new->setWebsite($website);

        $prices=$formule->getPrices();
        foreach ($prices as $price){
            $newprice= clone $price;
            if($newprice->getId()){
                $newprice->setId(null);
            }
            $new->addPrice($newprice);
        }

        $this->em->persist($new->getPrices()[0]);
        $this->em->persist($new);
        $this->em->flush();
        return $new;

    }
  */

}