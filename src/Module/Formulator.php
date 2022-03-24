<?php


namespace App\Module;

use App\Entity\Agenda\Appointments;
use App\Entity\Food\ArticlesFormule;
use App\Entity\Module\Formules;
use App\Lib\MsgAjax;
use App\Repository\Entity\ArticlesFormuleRepository;
use App\Service\Media\Uploadator;
use App\Module\Evenator;
use App\Util\CalDateAppointement;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;


class Formulator
{

    private EntityManagerInterface $em;
    private Evenator|CalDateAppointement $calldate;
    private ArticlesFormuleRepository $repoarticle;

    /**
     * Postar constructor.
     * @param EntityManagerInterface $entityManager
     * @param CalDateAppointement $calldate
     * @param ArticlesFormuleRepository $articlesFormuleRepository
     */
    public function __construct(EntityManagerInterface $entityManager, CalDateAppointement $calldate, ArticlesFormuleRepository $articlesFormuleRepository)
    {
        $this->em = $entityManager;
        $this->calldate = $calldate;
        $this->repoarticle =$articlesFormuleRepository;
    }

    /**
     * @param $form
     * @param $formule
     * @param $website
     * @param $articles
     * @param $key
     * @return array
     */
    public function newFormule($form, $formule, $website, $articles, $key): array
    {
        if(!$formule->getName()) $formule->setName('Menu du jour');

        $appointment = new Appointments();
        // on instancie la parution(appointements)
        $this->parution = $formule->setParution($this->calldate->alongDaysFormule($website,$form, $appointment));
        $formule->setKeymodule($key);
        /** @var ArticlesFormule $art */

        foreach ($articles as $artcat) {
            foreach ($artcat as $art){
                $adart=$this->repoarticle->find($art);
                if($adart){
                    $formule->addArticle($adart);
                }
            }
        }

        $this->em->persist($formule);
        $this->em->flush();
        return MsgAjax::MSG_POSTOK;
    }

    public function getlistarticles($formule): array
    {
        /** @var $artcat ArticlesFormule */
        foreach ($formule->getArticles() as $artcat) {
            $tablistart[]=$artcat->getId();
        }
        return $tablistart??[];
    }

    /**
     * @param $form
     * @param $formule Formules
     * @param $articles
     * @param $website
     * @return array
     */
    public function editFormule($form, Formules $formule, $articles, $website): array
    {
        if(!$formule->getName()) $formule->setName('Menu du jour');
        if(!empty($articles)){
            foreach ($formule->getArticles() as $oldarticle){
                $formule->removeArticle($oldarticle);
            }
        }

        $appointment=$formule->getParution();
        $formule->setParution($this->calldate->alongDaysFormule($website,$form, $appointment));

        /*

        foreach ($form->get('articles')->getData() as $key => $art){
            if($basedata=$articles[$key]['base64']){
                list($type, $data) = explode(';', $basedata);
                list(, $data)      = explode(',', $basedata);
                $parts = base64_decode($data);
                $img = imagecreatefromstring($parts);
                if(!$img) die(); // todo faire mieux
                $uploadName = sha1(uniqid(mt_rand(), true));
                $namefile = $uploadName . '.' . 'jpg';

                if($pict=$art->getPict()){
                    if($art->getPict()->getNamefile()!=""){
                        $this->upload->removeUpload($art->getPict());
                    }
                }else{
                    $pict=new Pict();
                    $art->setPict($pict);
                }
                imagejpeg($img,$pict->getUploadRootDir() . '/' . $namefile);
                $pict->setNamefile($namefile);
                $this->em->persist($art);
            }
        }
*/
        foreach ($articles as $artcat) {
            foreach ($artcat as $art){
                $adart=$this->repoarticle->find($art);
                if($adart){
                    $formule->addArticle($adart);
                }
            }
        }
        $this->em->persist($formule);
        $this->em->flush();
        return MsgAjax::MSG_POSTOK;
    }

    /**
     * @param $website
     * @param $key
     * @param $formule Formules
     * @return Formules
     */
    public function duplicateFormule($website, $key, Formules $formule): Formules
    {

        $newformule=clone $formule;
        if($newformule->getId()) {
            $newformule->setId(null);
        }
        $newformule->setPublied(false);
        if($formule->getName()) $newformule->setName($formule->getName().'- copie');
        $appointment = new Appointments();
        // on instancie la parution(appointements)
        $newformule->setParution($this->calldate->alongDaysNow($website,$appointment));
        $newformule->setCreateAt(new DateTime());

        $catformule=$formule->getCatformules();
        foreach ($catformule as $cat){
            $newcat= clone $cat;
            if( $newcat->getId()){
                $newcat->setId(null);
            }
            $newformule->addCatformule($newcat);
        }

        $articles = $formule->getArticles();
        foreach ($articles as $article){
            $newformule->addArticle($article);
        }
        $newformule->setKeymodule($key);
        $newformule->setServices($formule->getServices());

        $this->em->persist($newformule);
        $this->em->flush();
        return $newformule;
    }

    /**
     * @param $formule Formules
     * @return array
     */
    public function removeFormule(Formules $formule): array
    {
        foreach ($formule->getCatformules() as $cat) {
            $this->em->remove($cat);
        }
        $this->em->remove($formule);
        $this->em->flush();
        return MsgAjax::MSG_POSTOK;
    }

    /**
     * @param $formule Formules
     * @return array
     */
    public function publiedFormule(Formules $formule): array
    {
        $formule->setPublied(!$formule->getPublied());
        $this->em->persist($formule);
        $this->em->flush();
        return MsgAjax::MSG_POSTOK;
    }
}