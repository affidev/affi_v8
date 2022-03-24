<?php


namespace App\Controller\Modules;


use App\Classe\affisession;
use App\Entity\Agenda\Appointments;
use App\Entity\Module\ModuleTypes;
use App\Entity\Module\PostEvent;
use App\Form\ModMarketType;
use App\Form\PostEventType;
use App\Repository\Entity\WebsiteRepository;
use App\Service\Modules\Marketcator;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/module/market")
 * @IsGranted("ROLE_CUSTOMER")
 */

class MarketController extends AbstractController
{
    use affisession;


/*
    /**
     * @Route("/module-marche/{id}", name="new_module_3")
     * @param Request $request
     * @param WebsiteRepository $websiteRepository
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function preMarket(Request $request,WebsiteRepository $websiteRepository, $id)
    {
        $spaceWeb=$websiteRepository->find($id);
        if(!$spaceWeb)return $this->redirectToRoute('cargo_public');
        $module=new ModuleTypes();
        $module->setTypemodule("market");
        $module->setPostEvent($postevent=new PostEvent);
        $spaceWeb->addModule($module);
        $form=$this->createForm(PostEventType::class, $postevent);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){ // TODO faire la validation
            $module->setName($form->get('titre')->getData());
            $this->em->persist($module);
            $this->em->flush();
            return $this->redirectToRoute('appoint_market',[
                'id'=>$module->getId(),
            ]);
        }
        $vartwig=$this->dispatch->templatingspaceWeb('main_spaceweb/market/postform','crea module market');
        return $this->render('layout/layout_mainprovider.html.twig', [
            'agent'=>$this->useragent,
            'form' => $form->createView(),
            'spaceweb'=>$spaceWeb,
            'vartwig'=>$vartwig,
            'admin'=>$this->admin
        ]);
    }

/*

    /**
     * @Route("/module-marche-date/{id}", name="appoint_market")
     * @param Request $request
     * @param Marketcator $marketcator
     * @param null $addate
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function newDateMarket(ModuleTypes $module, Request $request, Marketcator $marketcator, $addate=null)
    {
        $marketation=new Eventation();
        $form=$this->createForm(ModMarketType::class, $marketation);
        $form=$marketcator->newMarket($form, new DateTime());
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){ // TODO faire la validation
            $marketation->setModuletype($module);
            $appointment = new Appointments();

            // on instancie une periode pour ce marché
            $Instperiods=$marketcator->addPeriodMarket($appointment, $form);
            $this->em->persist($Instperiods);

            // on deasactive cette partie pour l'instant
            /*
            $callbackappoint= $form->get("callback")->getData();
              if($callbackappoint>0){
              $instCallbacksAppoint=$marketcator->addCallBack($appointment, $callbackappoint);
              $em->persist($instCallbacksAppoint);
              }
            */
            $appointment=$marketcator->addAppointMarket($appointment, $form);
            $appointment->setTypeAppointment(3); //todo pas oublié c'est ici que l'on attribut le type de module
            $this->em->persist($appointment);

            $marketation->setAppointment($appointment);

            /* todo base sur les adresses
            $repsector=$this->em->getRepository('App:Users\Sectorscity');
            $lat=$form->get('latitude')->getData();
            $long=$form->get('longitude')->getData();
            if($lat !=null && $long !=null){
                $market->setLatitude(floatval($lat));
                $market->setLongitude(floatval($long));
            }
            $postville=$form->get("locality")->getData();
            if($postville){
                $idsector=$repsector->findOneBy(array('ville'=>$postville));
                $market->setSector($idsector);
            }elseif($postcd=$form->get("postal_code")->getData()){
                $idsector=$repsector->findOneBy(array('codepostal'=>$postcd));
                $market->setSector($idsector);
            }else{
                return $this->render('desk/main_modules\layout_module_market.html.twig', ['form' => $form->createView(), 'titrepage' => 'ajout date de marché']);
            }

            $market->setSector($idsector);
            */

            $this->em->persist($module);
            $this->em->persist($marketation);
            $this->em->flush();
            $this->addFlash('newmarket', 'nouvelle date de marché enregistré.');
            return $this->redirectToRoute('spaceweb.index');
        }
        $vartwig=$this->dispatch->templatingprovider('main_spaceweb/market/addmarket','ajout date de marché');
        return $this->render('layout/layout_form_market.html.twig', [
            'agent'=>$this->useragent, //layout form car js via encore
            'form' => $form->createView(),
            'vartwig'=>$vartwig,
            'admin'=>$this->admin
        ]);
    }

/*
    /**
     * @Route("/module-marche", name="new_module_3")
     * @param Marketar $marketar
     * @param Marketcator $marketcator
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function newMarket(Marketar $marketar,  Marketcator $marketcator, Request $request)
    {

        $marketation = new Eventation();
        $form = $this->createForm(ModMarketType::class, $marketation);
        $form = $marketcator->newMarket($form, new DateTime());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { // TODO faire la validation

            $this->em->persist($marketation);
            $this->em->flush();
            $postevent = $marketar->newMarketar(['form'=>$form, 'market'=>$marketation,'spaceweb'=>$spaceWeb,'order'=>1]);
            $this->addFlash('newmarket', 'nouvelle date de marché enregistré.');
            return $this->redirectToRoute('spaceweb.index');
        }
        $vartwig=$this->dispatch->templatingspaceWeb('main_spaceweb/market/addmarket','nouveau marché');
        return $this->render('layout/layout_market.html.twig', [
            'agent'=>$this->useragent,
            'form' => $form->createView(),
            'vartwig'=>$vartwig,
            'admin'=>$this->admin
        ]);
    }

/*

    /**
     * @Route("/edit-market/{id}", name="edit_market")  //todo a revoir
     * @param int $id
     * @param Marketcator $marketcator
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editMarket(int $id, Marketcator $marketcator, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $repository=$em->getRepository('App:Market');

        $market = $repository->find($id);
        if(!$market){
            throw new NotFoundHttpException("aucun evenement enregistré"); // ou creer une page 404
        }

        $form=$this->createForm(AddMarketType::class, $market);

        $appointment=$market->getIdEvent();
        $periods=$appointment->getIdPeriods();

        $form=$marketcator->editMarket($appointment, $periods, $form);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $Instperiod=$marketcator->majPeriodMarket($periods, $form);
            $em->persist($Instperiod);

            // on deasactive cette partie pour l'instant
            /*
            $callbackappoint= $form->get("callback")->getData();
              if($callbackappoint>0){
              $instCallbacksAppoint=$marketcator->addCallBack($appointment, $callbackappoint);
              $em->persist($instCallbacksAppoint);
              }
            */
            $appointment=$marketcator->majAppointMarket($appointment, $form);
            $em->persist($appointment);


            $repsector=$em->getRepository('App:Sectorscity');

            $lat=$form->get('latitude')->getData();
            $long=$form->get('longitude')->getData();
            if($lat !=null && $long !=null){
                $market->setLatitude(floatval($lat));
                $market->setLongitude(floatval($long));
            }

            $postville=$form->get("locality")->getData();
            if($postville){ // c'est qu'il y a un changement d'adresse
                $idsector=$repsector->findOneBy(array('ville'=>$postville));
                $market->setLocalite($idsector);
            }

            $market->setLocalite($idsector);
            $em->persist($market);

            $em->flush();

            return $this->redirectToRoute('markets');

        }

        return $this->render('member/market/addmarket.html.twig', [
            'agent'=>$this->useragent,
            'form' => $form->createView(),
            'titrepage' => 'ajout date de marché',
            'market'=>$market->getId(),
            'admin'=>$this->admin
        ]);
    }

/*
    /**
     * @Route("/delete-market/{id}", name="marketdelete")
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteMarket(int $id, Request $request) //todo a revoir
    {

        $em = $this->getDoctrine()->getManager();
        $repository=$em->getRepository('App:Market');

        $market = $repository->find($id);
        if(!$market){
            throw new NotFoundHttpException("aucun evenement enregistré"); // ou creer une page 404
        }

        // On crée un formulaire vide, qui ne contiendra que le champ CSRF
        // Cela permet de protéger la suppression d'annonce contre cette faille
        $form = $this->get('form.factory')->create();

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $appointment=$market->getIdEvent();
            $periods=$appointment->getIdPeriods();
            foreach ($periods as $periode) {
                $em->remove($periode);
            }
            $logs=$appointment->getLogsAppointment();
            foreach ($logs as $log) {
                $em->remove($log);
            }
            $em->remove($appointment);
            $em->remove($market);
            $em->flush();

            $this->addFlash('info', "La date de marché a bien été supprimée.");

            return $this->redirectToRoute('markets');
        }

        return $this->render('member/market/deletemarket.html.twig', array(
            'agent'=>$this->useragent,
            'market' => $market,
            'form'   => $form->createView(),
            'admin'=>$this->admin
        ));
    }


}