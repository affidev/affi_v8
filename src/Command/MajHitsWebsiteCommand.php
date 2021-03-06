<?php

namespace App\Command;

use App\Entity\Module\Contactation;
use App\Entity\Module\ModuleList;
use App\Entity\UserMap\Hits;
use App\Entity\UserMap\Tagcat;
use App\Entity\UserMap\Taguery;
use App\Lib\Tools;
use App\Repository\Entity\TagcatRepository;
use App\Repository\Entity\TagueryRepository;
use App\Repository\Entity\WebsiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


class MajHitsWebsiteCommand extends Command
{
    protected static $defaultName = 'app:majhits';
    private EntityManagerInterface $em;
    private WebsiteRepository $websiteRepo;
    private TagueryRepository $tagueryRepository;



    public function __construct(
        EntityManagerInterface $em,
        WebsiteRepository $websiteRepo,
        TagueryRepository $tagueryRepository
    )
    {
        parent::__construct();
        $this->em = $em;
        $this->websiteRepo = $websiteRepo;
        $this->tagueryRepository=$tagueryRepository;

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        //$this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $io->progressStart();
        $items = $this->websiteRepo->findAll();

        // on en profite pour cleaner les noms des tagueries
        /*
        $tagitems = $this->tagueryRepository->findAll();
        foreach ($tagitems as $itemtag) {
            $itemtag->setName(tools::clean($itemtag->getName()));
            $this->em->persist($itemtag);
        }
        $this->em->flush();
*/
        foreach ($items as $item) {
            $io->progressAdvance();
            if (!$hit=$item->getHits()) {
                $hit = new Hits();
                $hit->setWebsite($item);
                $item->setHits($hit);
                $hit->setGps($item->getLocality());
            }else{
                $activities=$item->getTemplate()->getActivities();
                $tabactivities=explode(',',$activities);

                $cp=1; // pour les activit??s du website, on clean et on cree une tagcat pour chaque et leur index les tagueries
                foreach ($tabactivities as $activity){
                    $string=tools::clean($activity);
                    $tagcat=new Tagcat();
                    $tagcat->setName($string);
                    $tagcat->setPonderation($cp);
                        foreach ($item->getTemplate()->getTagueries() as $tb){
                           $tagcat->addTaguery($tb);
                        }
                    $hit->addCatag($tagcat);
                    $cp++;
                    $this->em->persist($tagcat);
                    }
                }
            $this->em->persist($hit);
            $this->em->persist($item);
            $this->em->flush();
            }
        $io->progressFinish();
        $io->success('Les hits des website ont ??t?? cr??es');

        return 0;
    }

}