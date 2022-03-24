<?php


namespace App\Bulle;


use App\Entity\Bulles\Bulle;
use App\Lib\MsgAjax;
use App\Repository\Entity\BulleRepository;
use App\Repository\Entity\TagueryRepository;
use App\Service\Bulle\Bullatorette;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;



class Bullator
{
    private DateTimeImmutable $now;
    private EntityManagerInterface $em;
    private TagueryRepository $tagueryRepository;
    private Bullatorette $bullatorette;
    private BulleRepository $bullerepo;


    /**
     * Postar constructor.
     * @param EntityManagerInterface $entityManager
     * @param Bullatorette $bullatorette
     * @param BulleRepository $bulleRepository
     * @param TagueryRepository $tagueryRepository
     */
    public function __construct(EntityManagerInterface $entityManager, Bullatorette $bullatorette,BulleRepository $bulleRepository, TagueryRepository $tagueryRepository)
    {
        $this->em=$entityManager;
        $this->bullatorette = $bullatorette;
        $this->now= New DateTimeImmutable();
        $this->tagueryRepository = $tagueryRepository;
        $this->bullerepo=$bulleRepository;
    }


    /**
     * @param $data
     * @return array
     */
    public function newBulle($data): array
    {

        if(!$data)return MsgAjax::MSG_BULL0;

        if(!$bulle=$this->bullerepo->findOneBy(['idmodule'=>$data['id']])){
            $bulle = new Bulle();
            $bulle->setQuality(0);
            $bulle->setValid(true);
            $bulle->setDeleted(false);
            $bulle->setNbrvieuw(1);
            $bulle->setNbreponse(0);
            $bulle->setLasttripAt($this->now);
            $bulle->setSpacevisiting("space");
            $bulle->setExpireAt($this->now->modify('+8 day'));
            $bulle->setDateUpdate($this->now);
            $bulle->setIdmodule(intval($data['id']));
            $bulle->setModulebubble($data['module']);
            $bulle->addDispatchp($data['dispatch']);
/*
            $tabtags=explode(',',$data["tag"]);
            if($tabtags != ""){
                foreach ($tabtags as $tag){
                    if($tagentity=$this->tagueryRepository->findOneBy(['name'=>$tag])){
                        $bulle->addTaguery($tagentity);
                    }

                }
            }
*/
        }else{
            $bulle->setNbrvieuw($bulle->getNbrvieuw()+1);
            $data['dispatch']->addBulle($bulle);
        }

        $this->em->persist($bulle);
        $this->em->persist($data['dispatch']);
        $this->em->flush();
        $issue=MsgAjax::MSG_COMLETED;
        $issue['bulle']=$bulle->getId();
        return $issue;
    }


    public function addBullette($bulle, $content, $dispatch){
        $bulle->addBullette($this->bullatorette->newBullette([
            'bubble'=>$bulle,
            'content'=>$content,
            'spaceWeb'=>$dispatch]));

        $this->em->persist($bulle);
        $this->em->flush();
    }

    public function deleteBulle($idBulle, $dispatch): bool
    {
        if($bulle=$this->bullerepo->find($idBulle)) {
            $bulle->removeDispatchp($dispatch);
            $this->em->persist($bulle);
            $this->em->persist($dispatch);
            $this->em->flush();
            return true;
        }else{
            return false;
        }
    }



}