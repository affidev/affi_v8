<?php

namespace App\Controller\Page;

use App\Classe\affisession;
use App\Entity\Sector\Gps;
use App\Entity\Websites\Website;
use App\Lib\Links;
use App\Repository\Entity\WebsiteRepository;
use App\Service\Localisation\LocalisationServices;
use App\Service\Search\Listpublications;
use App\Util\DefaultModules;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


class RessourceNumController extends AbstractController
{

 use affisession;

    /**
     * @Route("/ressources-numeriques", options={"expose"=true},  name="ressources_num_list")
     * @param Request $request
     * @param LocalisationServices $locateService
     * @return Response
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function ressourcesNumList(Request $request,LocalisationServices $locateService): Response
    {


        if ($this->security->isGranted('ROLE_CUSTOMER')) {
            if(!$this->dispatch) throw new Exception('dispatch inconnu');

            //$notifdispatch=$this->dispatch->getTbnotifs();

            if($slugcity){
                $locate=$locateService->findLocate($slugcity);
                if(!$locate instanceof Gps) throw new Exception('ville inconnue');
            }else {
                $locate = $this->dispatch->getLocality();
            }
            $this->activeBoard();
            $vartwig=$this->menuNav->templatingspaceWeb(
                'affipublic',
                $this->board->getNamewebsite(),
                $this->board
            );

            //  $bulles=$this->intiTabbulle($locate->getSlugcity(), $vartwig['tabActivities']);
           // $this->intisimplCatchs();

        }else{
            if($slugcity){
                $locate=$locateService->findLocate($slugcity);
                if(!$locate instanceof Gps) throw new Exception('ville inconnue');
            }else {
                $locate = false;
            }

            $vartwig=$this->menuNav->templateControl(
                Links::PUBLIC,
                $locate ?'affipublic':"affihome",
                "AffiChanGe, web local",
                'all');
        }

        if($locate){
            /** @var Website $wbsitesloc */
            $wbsitesloc = $websiteRepository->findWebsiteOfLocate($locate);

            foreach ($wbsitesloc as $wb){
                $listboard[$wb['codesite']]=$wb;
                if( $wb['statut']) $tabloc[]=$wb;// pour identification du board de la marie locale
            }
        }

        return $this->render('aff_public/home.html.twig', [
            'directory'=>$locate ?'locate':"nolocate",
            'replacejs'=>!empty($lastnotices),
            'vartwig'=>$vartwig,
            'locate'=>$locate,
            'city'=>$locate?$locate->getCity():false,
            'wbsiteloc'=>$wbsitesloc??null,
            'wbsiteelse'=>[], //todo pour l'instant à voir si besoin mais est dans le template
            'official'=>$tabloc??null,
            'board'=>$this->board??[],
            'dispatch'=>$this->dispatch??null,
            //'lastsnotice'=>$lastnotices??[],
            'catchs'=>$this->catchs,
            "pws"=>$tabwebsite??[],
            "notifs"=>$notifdispatch??[],
            //'cargo'=>$bulles?json_encode($bulles):null,
            'tag'=>['name'=>$city??null,'active'=>true,'l_class'=>"cargo"],  // l_class pour la redirection du plugin localitate (choix posible : cargo,customer, init)
        ]);
    }
}
