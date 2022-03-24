<?php


namespace App\Service\SpaceWeb;

use App\Entity\UserMap\Hits;
use App\Entity\UserMap\Tagcat;
use App\Lib\Tools;
use App\Repository\Entity\TagueryRepository;
use App\Repository\Entity\WebsiteRepository;
use Doctrine\ORM\EntityManagerInterface;

class Tagatot
{
    private EntityManagerInterface $em;


    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    public function majTagCat($website){

        if (!$hit=$website->getHits()) {
            $hit = new Hits();
            $hit->setWebsite($website);
            $website->setHits($hit);
            $hit->setGps($website->getLocality());
        }

        $tagcatswb=$hit->getCatag();
        $tttab=[];
        foreach ($tagcatswb as $tt) {
            $tttab[] = $tt->getName();
        }

        $activities=$website->getTemplate()->getActivities();
        $tabactivities=explode(',',$activities);

        $cp=1; // pour les activités du website, on clean et on cree une tagcat pour chaque et leur index les tagueries
        foreach ($tabactivities as $activity){
            $string=tools::clean($activity);
            if(!in_array($string,$tttab)) {
                $tagcat = new Tagcat();
                $tagcat->setName($string);
                $tagcat->setPonderation($cp);
                foreach ($website->getTemplate()->getTagueries() as $tb) {
                    $tagcat->addTaguery($tb);
                }
                $hit->addCatag($tagcat);
                $cp++;
                $this->em->persist($tagcat);
            }
        }
        $this->em->persist($hit);
        $this->em->persist($website);
        $this->em->flush();
    }
}