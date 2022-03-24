<?php


namespace App\Controller\Modules;

use App\Classe\affisession;
use App\Entity\Module\Contactation;
use App\Entity\Websites\Opendays;
use App\Entity\Websites\Website;
use App\Form\ContactResaType;
use App\Module\Contactar;
use App\Repository\Entity\ContactationRepository;
use App\Repository\Entity\ResaRepository;
use App\Repository\Entity\SpwsiteRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/module/resa")
 * @IsGranted("ROLE_CUSTOMER")
 */

class ResaController extends AbstractController
{

    use affisession;


    /**
     * @Route("/process-module-reservation/{slug}", options={"expose"=true}, name="process_resa")
     * @var $opendays Opendays
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function processResa(SpwsiteRepository $spwsiteRepository, $slug)
    {
        $spwsite=$spwsiteRepository->getOneSpwWithWebsiteThanDispathBySlug($slug, $this->iddispatch);
        if(!$spwsite) return $this->redirectToRoute('cargo_public');
        /** @var Website $website */
        $website=$spwsite->getWebsite();
        /** @var Opendays $opendays */
        $openday=($opendays=$website->getTabopendays())?true:false;
        if($openday){
            $tabunique=$opendays->getTabunique();
            $tabconges=$opendays->getConges();
        }else{
            $tabunique="";
            $tabconges="";
        }

        $sector=$website->getTemplate()->getSector()?true:false;
        $vartwig=$this->dispatch->templatingspaceWeb(
            'main_spaceweb/modules/process-resa',
            'creation module reservation');

        return $this->render('layout/layout_mainprocessresa.html.twig', [
            'agent'=>$this->useragent,
            'vartwig' => $vartwig,
            'openday' => $openday,
            'twigtbunique'=>$tabunique,
            'twigconges'=>$tabconges,
            'spaceweb'=>$website,
            'sector'=>$sector,
            'admin'=>$this->admin
        ]);
    }


    /*===================================== fin function ok ====================================================
   |
   ============================================================================================================*/

    /**
     * @Route("/creation-module-reservation/{slug}", name="new_resa_mod")
     * @param Request $request
     * @param Contactar $contactar
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function newModResa(Request $request, Contactar $contactar, SpwsiteRepository $spwsiteRepository, $slug)
    {
        //todo voir si le openday est existant
        $spwsite=$spwsiteRepository->getOneSpwWithWebsiteThanDispathBySlug($slug, $this->iddispatch);
        if(!$spwsite) return $this->redirectToRoute('cargo_public');
        /** @var Website $website */
        $website=$spwsite->getWebsite();
        $contactation = new Contactation();
        //todo rajouter la selection des jours ouverts et horaires
        $form=$this->createForm(ContactResaType::class, $contactation);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) { // TODO faire la validation
            $this->em->persist($contactation);
            $this->em->flush();
            $issue = $contactar->newContactor(['module' => 'resa', 'form' => $form, 'website' => $website, 'contactation' => $contactation]);
            $this->addFlash('infoprovider', 'nouveau module de reservation ok.');
            return $this->redirectToRoute('spaceweb_mod', ['slug' => $website->getSlug()]);
        }
        $vartwig=$this->dispatch->templatingspaceWeb(
            'website/resa/addresa',
            'crea module reservation');
        return $this->render('layout/layout_resa.html.twig', [
            'agent'=>$this->useragent,
            'form' => $form->createView(),
            'spaceweb'=>$website,
            'vartwig'=>$vartwig,
            'admin'=>$this->admin
        ]);
    }

/*
    /**
     * @Route("/modif-reservation/{id}", name="edit_resa")
     * @param $id
     * @param ContactationRepository $contactationRepository
     * @param Request $request
     * @param Contactar $contactar
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editModResa($id, ContactationRepository $contactationRepository,Request $request, Contactar $contactar)
    {
        $contactation=$contactationRepository->find($id);
        $module=$contactation->getIdmodule();

        $website=$module->getWebsite();
        $postevent=$module->getPostevent();

        $form=$this->createForm(ContactResaType::class, $contactation);
        $form=$contactar->initFormContactor($form,$postevent);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){ // TODO faire la validation
            $this->em->flush();
            $issue=$contactar->modifContactor(['form'=>$form, 'contactation'=>$contactation]);
            $this->addFlash('infoprovider', 'module reservation mis a jour.');
            return $this->redirectToRoute('spaceweb.index');

        }
        $vartwig=$this->dispatch->templatingspaceWeb('main_spaceweb/resa/addresa','crea module reservation');
        return $this->render('layout/layout_resa.html.twig', [
            'agent'=>$this->useragent,
            'form' => $form->createView(),
            'spaceweb'=>$website,
            'vartwig'=>$vartwig,
            'admin'=>$this->admin
        ]);

    }

/*
    // todo delete module resa

    /**
     * @Route("/show-reservation/{start}/{end}", name="show_resa")
     * @param $id
     * @param ResaRepository $resaRepository
     * @param null $start
     * @param null $end
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function showResa(ResaRepository $resaRepository,$start=null, $end=null)
    {
        if(!$this->session->get('spaceweb'))return $this->redirectToRoute('cargo_public');
        $spaceWeb=$spaceWebRepository->find($this->spacewebid);
        if(!$spaceWeb->hasLmodules('RESATAR'))return $this->redirectToRoute('process_resa');
        if($start==null || $end==null){
            $start=new DateTime();
            $end=new DateTime();
        }
        $resa = $resaRepository->findBySpaceWebForDate($this->spacewebid,$start,$end);

        $vartwig=$this->dispatch->templatingspaceWeb('main_spaceweb/resa/boardersa','board reservation');
        return $this->render('layout/layout_resa.html.twig', [
            'agent'=>$this->useragent,
            'spaceweb'=>$spaceWeb,
            'resas'=>$resa,
            'vartwig'=>$vartwig,
            'admin'=>$this->admin
        ]);
    }


}