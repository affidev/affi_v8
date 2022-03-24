<?php


namespace App\Controller\MainPublic;

use App\Classe\affisession;
use App\Repository\Entity\WebsiteRepository;
use App\Service\Search\Searchmodule;
use App\Util\DefaultModules;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PublicationWebsiteController extends AbstractController
{
    use affisession;

    /**
     * @Route("/affiche/{slugcity}/{slug}/{posta}/{id}", options={"expose"=true}, name="show_post", requirements={"slug":"[a-z0-9\-]*"})
     * @param $id
     * @param Searchmodule $searchmodule
     * @param $slug
     * @param $slugcity
     * @return Response
     * @throws NonUniqueResultException
     */
    public function showPost($id, Searchmodule $searchmodule, $slug, $slugcity): Response
    {
        $tab=$searchmodule->searchOnePostAndListAndMsg($id);

        if(!$tab['post'])return $this->redirectToRoute('show_website',['slugcity'=>$this->board->getLocality()->getSlugcity(),'slug'=>$this->board->getSlug()]);
        $this->board=$tab['board'];
        if($this->isGranted('ROLE_CUSTOMER') && $this->dispatch){
            $vartwig=$this->menuNav->templatingspaceWeb('showpost',$this->board->getNamewebsite(),$this->board);
            $this->intisimplCatchs();

            foreach ($this->catchs['blog'] as $value) {
                if (strval($value->getIdmodule())== $id) {
                    $iscatch = $value->getId();
                    break;
                }
            }
            /*
            if($this->isPwDispatch($this->board)) {
                foreach ($vartwig['tabActivities'] as $module) {
                    $bulles[] = ['name' => DefaultModules::TAB_MODULES_NAME[$module], 'url' => $this->generateUrl(DefaultModules::TAB_MODULES_URL_ID[$module], ['id' => $this->board->getId()], UrlGeneratorInterface::ABSOLUTE_URL)];
                }
            }
            */
        }else{
            $vartwig = $this->menuNav->postinfoObj($tab['post'],$this->board, 'showpost', $tab['post']->getTitre(), 'all');
        }

        return $this->render('aff_website/home.html.twig', [
            'directory'=>'post',
            'replacejs'=>!empty($tab['posts']),
            'vartwig' => $vartwig,
            'dispatch'=>$this->dispatch??null,
            'pw'=>$this->pw??false,
            'content'=>$tab['content'],
            'mcdata'=>true,
            'post'=>$tab['post'],
            'posts'=>$tab['posts'],
            'iscatch'=>$iscatch??null,
            'typ'=>"blog",
            'entity'=>$tab['post']->getId(),
            'catchs'=>$this->catchs,
            'website'=>$this->board,
            'board'=>$this->board,
        ]);
    }

    /**
     * @Route("/promo/{slugcity}/{slug}/{offra}/{id}", options={"expose"=true}, name="show_offre", requirements={"slug":"[a-z0-9\-]*"})
     * @param $id
     * @param Searchmodule $searchmodule
     * @param $slug
     * @param $slugcity
     * @return Response
     * @throws NonUniqueResultException
     */
    public function showOffre($id,Searchmodule $searchmodule, $slug, $slugcity): Response
    {
        $tab=$searchmodule->searchOneOffreandList($id);
        if(!$tab['offre'])return $this->redirectToRoute('show_website',['slugcity'=>$slugcity,'slug'=>$slug]);
        $this->board=$tab['board'];

        if($this->isGranted('ROLE_CUSTOMER') && $this->dispatch){
            $vartwig=$this->menuNav->templatingspaceWeb('showoffre',$this->board->getNamewebsite(),$this->board);

            $this->intisimplCatchs();

            foreach ($this->catchs['shop'] as $value) {
                if (strval($value->getIdmodule())== $id) {
                    $iscatch = $value->getId();
                    break;
                }
            }
            /*
            if($this->isPwDispatch($this->board)) {
                foreach ($vartwig['tabActivities'] as $module) {
                    $bulles[] = ['name' => DefaultModules::TAB_MODULES_NAME[$module], 'url' => $this->generateUrl(DefaultModules::TAB_MODULES_URL_ID[$module], ['id' => $this->board->getId()], UrlGeneratorInterface::ABSOLUTE_URL)];
                }
            }
            */
        }else {
            $vartwig = $this->menuNav->websiteinfoObj($this->board, 'showoffre', $tab['offre']->getTitre(), 'all');
        }

        return $this->render('aff_website/home.html.twig', [
            'directory'=>'shop',
            'replacejs'=>!empty($tab['offres']),
            'vartwig' => $vartwig,
            'dispatch'=>$this->dispatch??null,
            'pw'=>$this->pw??false,
            'content'=>$tab['content'],
            'offre'=>$tab['offre'],
            'offres'=>$tab['offres'],
            'iscatch'=>$iscatch??null,
            'typ'=>"shop",
            'entity'=>$tab['offre']->getId(),
            'website'=>$this->board,
            'board'=>$this->board,
            'city'=>$this->board->getLocality()->getCity(),
        ]);
    }


    /**
     * @Route("/events/{slugcity}/{slug}/{id}", options={"expose"=true}, name="show_event", requirements={"slug":"[a-z0-9\-]*"})
     * @param Searchmodule $searchmodule
     * @param WebsiteRepository $websiteRepository
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     */
    public function showEvent(Searchmodule $searchmodule,  WebsiteRepository $websiteRepository, $id): Response
    {
        $tab = $searchmodule->searchOneEventandList($id);
        if (!$tab['event']) return $this->redirectToRoute('show_website', ['slugcity' => $this->board->getLocality()->getSlugcity(), 'slug' => $this->board->getSlug()]);
        $this->board=$tab['board'];

        if ($this->isGranted('ROLE_CUSTOMER') && $this->dispatch) {
            $vartwig = $this->menuNav->templatingspaceWeb('showevent', $this->board->getNamewebsite(), $this->board);

/*
            $this->intisimplCatchs();
            foreach ($this->catchs['module_event'] as $value) {
                if (strval($value->getIdmodule())== $id) {
                    $iscatch = $value->getId();
                    break;
                }
            }
*/
            /*
            if ($this->isPwDispatch($this->board)) {
                foreach ($vartwig['tabActivities'] as $module) {
                    $bulles[] = ['name' => DefaultModules::TAB_MODULES_NAME[$module], 'url' => $this->generateUrl(DefaultModules::TAB_MODULES_URL_ID[$module], ['id' => $this->board->getId()], UrlGeneratorInterface::ABSOLUTE_URL)];
                }
            }
            */
        } else {
            $vartwig = $this->menuNav->websiteinfoObj($this->board, 'showevent', $tab['event']->getTitre(), 'all');
        }

        return $this->render('aff_website/home.html.twig', [
            'directory' => 'event',
            'replacejs'=>!empty($tab['events']),
            'vartwig' => $vartwig,
            'dispatch'=>$this->dispatch??null,
            'pw'=>$this->pw??false,
            'content' => "",
            'event' => $tab['event'],
            'events' => $tab['events'],
            'iscatch'=>null,
            'website' => $this->board,
            'board' => $this->board,
            'city' => $this->board->getLocality()->getCity(),
        ]);
    }


    /**
     * @Route("/formules/{slugcity}/{slug}/{id}", options={"expose"=true}, name="show_found", requirements={"slugcity":"[a-z0-9\-]*"})
     * @param Searchmodule $searchmodule
     * @param $slug
     * @param $slugcity
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     */
    public function showFormules(Searchmodule $searchmodule, $slug, $slugcity,$id): Response
    {
        $tab=$searchmodule->searchOneMenuandList($id);
        if(!$tab['menu'])return $this->redirectToRoute('show_website',['slugcity'=>$slugcity,'slug'=>$slug]);
        $this->board=$tab['board'];

        if($this->isGranted('ROLE_CUSTOMER') && $this->dispatch){
            $vartwig=$this->menuNav->templatingspaceWeb('showformule',$this->board->getNamewebsite(),$this->board);
            /*
            $this->intisimplCatchs();

            foreach ($this->catchs['module_found'] as $value) {
                if (strval($value->getIdmodule())== $id) {
                    $iscatch = $value->getId();
                    break;
                }
            }
            */
/*
            if($this->isPwDispatch($this->board)) {
                foreach ($vartwig['tabActivities'] as $module) {
                    $bulles[] = ['name' => DefaultModules::TAB_MODULES_NAME[$module], 'url' => $this->generateUrl(DefaultModules::TAB_MODULES_URL_ID[$module], ['id' => $this->board->getId()], UrlGeneratorInterface::ABSOLUTE_URL)];
                }
            }
*/
        }else {
            $vartwig = $this->menuNav->websiteinfoObj($this->board, 'showformule', $tab['menu']->getName(), 'all');
        }

        return $this->render('aff_website/home.html.twig', [
            'directory'=>'formule',
            'replacejs'=>!empty($tab['menu']),
            'vartwig' => $vartwig,
            'dispatch'=>$this->dispatch??null,
            'pw'=>$this->pw??false,
            'menu'=>$tab['menu'],
            'menus'=>$tab['menus'],
            'iscatch'=>null,
            'website'=>$this->board,
            'board'=>$this->board,
            'city'=>$this->board->getLocality()->getCity(),
        ]);
    }





}