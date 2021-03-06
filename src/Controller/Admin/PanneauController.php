<?php


namespace App\Controller\Admin;


use App\Classe\adminsession;
use App\Entity\Sector\Adresses;
use App\Entity\Websites\Opendays;
use App\Event\WebsiteCreatedEvent;
use App\Form\MailType;
use App\Form\WebsiteType;
use App\Lib\Links;
use App\Lib\MsgAjax;
use App\Repository\Entity\WebsiteRepository;
use App\Service\Localisation\LocalisationServices;
use App\Service\SpaceWeb\SpacewebFactor;
use App\Service\SpaceWeb\Tagatot;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @Route("/board-affilink/panneaux/")
 * @IsGranted("ROLE_SUPER_ADMIN")
 */

class PanneauController extends AbstractController
{

    use adminsession;

    /**
     * @Route("new/", name="new_panneau_admin")
     * @param Request $request
     * @param SpacewebFactor $spaceWebtor
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    public function newPanneauAdmin(Request $request,SpacewebFactor $spaceWebtor, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $form = $this->createFormBuilder()
        ->add('idcity', HiddenType::class)
        ->add('namewebsite', HiddenType::class)
        ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $website = $spaceWebtor->addWebsiteLocalityAdmin($this->dispatch,  $form);
            $key=$website->getSlug().sha1(uniqid(mt_rand(), true));
            $website->setCodesite($key);
            $em->persist($website);
            $em->flush();
            return $this->redirectToRoute('edit_panneau_admin',['id'=>$website->getId()]);
        }

        $vartwig=$this->menuNav->templateControl(
            Links::CUSTOMER_LIST,
            'newpanneau',
            "",
            'all');

        return $this->render('aff_master/home.html.twig', [
            'directory'=>'website',
            'customer'=>$this->dispatch,
            'form'=>$form->createView(),
            'vartwig'=>$vartwig
        ]);
    }

    /**
     * @Route("edit/{id}", name="edit_panneau_admin")
     * @param Request $request
     * @param EventDispatcherInterface $dispatcher
     * @param Tagatot $tagatot
     * @param SpacewebFactor $spaceWebtor
     * @param WebsiteRepository $websiteRepository
     * @param $id
     * @return Response
     */
    public function editPanneauAdmin(Request $request,EventDispatcherInterface $dispatcher,Tagatot $tagatot, SpacewebFactor $spaceWebtor,WebsiteRepository $websiteRepository, $id): Response
    {
        $website=$websiteRepository->find($id);
        $tags=$website->getTemplate()->getTagueries();
        $tx="";
        foreach ($tags as $tag){
            $tx.=html_entity_decode ($tag->getName()).",";
        }
        $form=$this->createForm(WebsiteType::class,$website);
        $form['template']['tagueries']->setData($tx);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $website=$spaceWebtor->initTemplate($website, $form);
            $tagatot->majTagCat($website);
            $event= new WebsiteCreatedEvent($website);
            $dispatcher->dispatch($event, WebsiteCreatedEvent::CREATE);
            return $this->redirectToRoute('back_admin_websites',['keyboard'=>'v5-12020test']);
        }
        $vartwig=$this->menuNav->templateControl(
            Links::CUSTOMER_LIST,
            'editpanneau',
            "",
            'all');

        return $this->render('aff_master/home.html.twig', [
            'directory'=>'website',
            'customer'=>$this->dispatch,
            'form'=>$form->createView(),
            'board'=>$website,
            'website'=>$website,
            'vartwig'=>$vartwig
        ]);
    }

    /**
     * @Route("members/{id}", name="members_admin")
     * @param $id
     * @param Request $request
     * @param SpacewebFactor $spaceWebtor
     * @param WebsiteRepository $websiteRepository
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function membersWebsiteAdmin($id, Request $request,SpacewebFactor $spaceWebtor, WebsiteRepository $websiteRepository): RedirectResponse|Response
    {
        $website=$websiteRepository->find($id);
        $form=$this->createForm(MailType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $mail=$form['mail']->getData();
            if(!$dispatch=$this->getDispatchByEmail($mail)){ // todo : attention si email change c'est bien le bon ??
              /*  $tabmember=[
                    "contact"=>null,
                    "type"=>"member",
                    "board"=>$website,
                    "mail"=>$mail,
                    "pass"=>false,
                    "name"=>false];
              */
                $spaceWebtor->invitMailToAdmin($mail, $website); // dotation d'un website a un tiers (adresse mail, non contact) en tant que superadmin
            }else{
                $spaceWebtor->addwebsitedispatch($dispatch,$website);// dotation d'un website a un dispatch en tant que superadmin
            }
            return $this->redirectToRoute('members_admin', ['id'=>$website->getId()]);
        }

        $vartwig=$this->menuNav->templatingadmin(
            'members',
            'parametres du panneau',
            $website,1);

        return $this->render('aff_master/home.html.twig', [
            'form'=>$form->createView(),
            'board'=>$website,
            'website'=>$website,
            'dispatch'=>$this->dispatch,
            'directory'=>'website',
            'spwsites'=>$website->getSpwsites(),
            'vartwig'=>$vartwig,
        ]);
    }

    /**
     * imputation des adresses ?? un webspace
     *
     * @Route("localizeAdmin/{id}", name="spaceweblocalize_init_admin")
     * @IsGranted("ROLE_CUSTOMER")
     * @param WebsiteRepository $websiteRepository
     * @param $id
     * @return Response
     */
    public function localizeWebsiteAdmin(WebsiteRepository $websiteRepository, $id): Response
    {
        $board=$websiteRepository->find($id);

        $vartwig=$this->menuNav->templatingadmin(
            'localizer',
            'parametres du panneau',
            $board,3);

        return $this->render('aff_master/home.html.twig', [
            'directory'=>'website',
            'vartwig' => $vartwig,
            'board'=>$board,
            'website'=>$board,
            'dispatch'=>$this->dispatch,
        ]);
    }

    /**
     * @Route("newadressAdmin", options={"expose"=true}, name="newadress_admin", methods={"POST"})
     * @param Request $request
     * @param LocalisationServices $localisation
     * @param WebsiteRepository $websiteRepository
     * @return JsonResponse
     */
    public function newAdressWebsiteAdmin(Request $request, LocalisationServices $localisation, WebsiteRepository $websiteRepository): JsonResponse
    {
        if($request->isXmlHttpRequest())
        {
            $data = json_decode((string) $request->getContent(), true);
            $website=$websiteRepository->find($data['id']);
            if(!$website) return new JsonResponse(['success'=>false,'error'=>'merdum ici : id =>'.$data['id']]);
            $adress=$localisation->newAdress($data,  $website, 1);
            if($adress!=null){
                $website->setStatut(true);
                $this->em->persist($website);
                $this->em->flush();
                $responseCode = 200;
                http_response_code($responseCode);
                header('Content-Type: application/json');
                return new JsonResponse(['success'=>true, "label"=>$data['properties']['label']]);
            }
            return new JsonResponse(['success'=>false,"error"=>"adresse pas enregistr??e"]);
        }
        return new JsonResponse(['success'=>false,"error"=>"requete erreur"]);
    }

    /**
     * @Route("deleteadressAdmin/{id}", options={"expose"=true}, name="deleteadress_admin", methods={"DELETE"})
     * @param Request $request
     * @param WebsiteRepository $websiteRepository
     * @param $id
     * @return JsonResponse
     */
    public function deleteAdressWebsiteAdmin(Request $request, WebsiteRepository $websiteRepository, $id): JsonResponse
    {
        if($request->isXmlHttpRequest())
        {
            $idwebsite=$request->request->get('website');
            $website=$websiteRepository->find($idwebsite);
            if(!$website)  return new JsonResponse(['success'=>false,"error"=>"id spaceweb non reconnu"]);
            $adresses=$website->getTemplate()->getSector()->getAdresse();
            /** @var Adresses $adress */
            foreach ($adresses as $adress) {
                if ($adress->getId() == $id) {
                    $this->em->remove($adress);
                    $this->em->flush();
                    $responseCode = 200;
                    http_response_code($responseCode);
                    header('Content-Type: application/json');
                    return new JsonResponse(['success'=>true]);
                }
            }
        }
        return new JsonResponse(['success'=>false,"error"=>"requete ajax non reconnue"]);
    }

    /**
     * @Route("horaires/profil-opendaysAdmin/{id}", name="opendays_edit_admin")
     * @param $id
     * @param WebsiteRepository $websiteRepository
     * @return RedirectResponse|Response
     */
    public function editOpenDaysAdmin($id,WebsiteRepository $websiteRepository): RedirectResponse|Response //todo le re??sitory $id est un website
    {
        $board=$websiteRepository->find($id);
        $tabunique="";
        $tabconges="";
        if($opendays=$board->getTabopendays()){
            $tabunique=$opendays->getTabuniquejso()??[];
            $tabconges=$opendays->getCongesjso()??[];
        }

        $vartwig=$this->menuNav->templatingadmin(
            'openday',
            'parametres du panneau',
            $board,5);

        return $this->render('aff_master/home.html.twig', [
            'vartwig'=>$vartwig,
            'directory'=>'website',
            'board'=>$board,
            'website'=>$board,
            'twigtbunique'=>$tabunique,
            'twigconges'=>$tabconges,
        ]);
    }


    /**
     * init et edit opendays.
     *
     * @Route("init-opendays/jx", options={"expose"=true}, name="init-opendays-ajx")
     * @param Request $request
     * @param WebsiteRepository $websiteRepository
     * @return JsonResponse
     */
    public function majOpenDaysAjx(Request $request, WebsiteRepository $websiteRepository): JsonResponse
    {
        if($request->isXmlHttpRequest())
        {
            $data = json_decode((string) $request->getContent(), true);
            if(!$this->getUserspwsiteOfWebsiteSlug($data['slug']) || !$this->admin) return new JsonResponse(MsgAjax::MSG_ERR3);

            $opendays=$this->board->getTabopendays();
            if(!$opendays){
                $opendays=New Opendays();
                $this->board->setTabopendays($opendays);
            }
            $opendays->setTabunique($data['tabunique']);
            $opendays->setConges($data['conges']);
            $opendays->setCongesjso(json_decode($data['conges'], true));
            $opendays->setTabuniquejso(json_decode($data['tabunique'], true));
            //$result['notify']=$data['notify'];
            //$result['spaceweb']=$data['order'];
            //$result['order']=$data['spaceweb'];
            $this->em->persist($this->board);
            $this->em->flush();
            $issue=MsgAjax::MSG_COMLETED; // TODO pas sur que necessaire
            $issue['openday']=$opendays->getId();
            return new JsonResponse($issue);
        }else{
            return new JsonResponse(MsgAjax::MSG_ERR4);
        }
    }



}