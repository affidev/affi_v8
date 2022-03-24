<?php

namespace App\Controller\Website;


use App\Classe\affisession;
use App\Entity\Websites\Opendays;
use App\Event\WebsiteCreatedEvent;
use App\Form\WebsiteType;
use App\Lib\MsgAjax;
use App\Repository\Entity\OffresRepository;
use App\Repository\Entity\PostRepository;
use App\Repository\Entity\WebsiteRepository;
use App\Service\SpaceWeb\SpacewebFactor;
use App\Service\SpaceWeb\Tagatot;
use App\Util\DefaultModules;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @Route("/admin-website/param/")
 * @IsGranted("ROLE_CUSTOMER")
 */

class AdminWebsiteController extends AbstractController
{
    use affisession;


    /**
     * @Route("parameters/{id}", name="parameters")
     * @param $id
     * @param OffresRepository $offresRepository
     * @param PostRepository $postationRepository
     * @return Response
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function parametersWebsite($id, OffresRepository $offresRepository, PostRepository $postationRepository): Response
    {
        if(!$this->getUserspwsiteOfWebsite($id) || !$this->admin )$this->redirectToRoute('cargo_public');

        $vartwig=$this->menuNav->templatingadmin(
            'parameter',
            'parametres du panneau',
            $this->board,1);

        return $this->render('aff_websiteadmin/home.html.twig', [
            'directory'=>'parameters',
            'replacejs'=>false,
            'board'=>$this->board,
            'website'=>$this->board,
            'dispatch'=>$this->dispatch,
            'test1'=>(bool)(($email = $this->board->getTemplate()->getEmailspaceweb())),
            'test2'=>(bool)(($sector = $this->board->getTemplate()->getSector())),
            'email'=>$email,
            'posts'=>$postationRepository->countPost($this->board->getCodesite()),
            'shops'=>$offresRepository->countOffre($this->board->getCodesite()),
            'openday'=>$this->board->getTabopendays()??null,
            'vartwig'=>$vartwig,
            'admin'=>[$this->admin,$this->permission],
            'city'=>$this->board->getLocality()->getCity()
        ]);
    }

    /**
     * @Route("modules/{id}", options={"expose"=true}, name="spaceweb_mod")
     * @param $id
     * @param DefaultModules $defaultModules
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function tabModulesWp($id, DefaultModules $defaultModules): RedirectResponse|Response
    {
        if(!$this->getUserspwsiteOfWebsite($id) || !$this->admin )$this->redirectToRoute('cargo_public');
        $moduletab=$defaultModules->selectModule($this->board);


        $vartwig=$this->menuNav->templatingadmin(
        'stateModules',
        'parametres du panneau',
            $this->board,2);


        return $this->render('aff_websiteadmin/home.html.twig', [
            'directory'=>'parameters',
            'replacejs'=>false,
            'vartwig' => $vartwig,
            'board'=>$this->board,
            'website'=>$this->board,
            'dispatch'=>$this->dispatch,
            'pw'=>$this->pw,
            'tabmodule'=>$moduletab,
            'admin'=>[$this->admin,$this->permission],
            'city'=>$this->board->getLocality()->getCity()
        ]);
    }

    /**
     * @Route("edit-website/{id}", name="website_edit")
     * @param $id
     * @param Request $request
     * @param Tagatot $tagatot
     * @param SpacewebFactor $spaceWebtor
     * @param EventDispatcherInterface $dispatcher
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function editWebsite($id, Request $request,Tagatot $tagatot, SpacewebFactor $spaceWebtor,EventDispatcherInterface $dispatcher): RedirectResponse|Response
    {
        if(!$this->getUserspwsiteOfWebsite($id) || !$this->admin )$this->redirectToRoute('cargo_public');
        $tags=$this->board->getTemplate()->getTagueries();
        $tx="";
        foreach ($tags as $tag){
            $tx.=html_entity_decode ($tag->getName()).",";
        }
        $form=$this->createForm(WebsiteType::class,$this->board);
        $form['template']['tagueries']->setData($tx);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $website=$spaceWebtor->initTemplate($this->board, $form);
            $tagatot->majTagCat($website);
            //suprimé en dev // todo ne pas oublier de remettre actif en televersement
            $event= new WebsiteCreatedEvent($website);
            $dispatcher->dispatch($event, WebsiteCreatedEvent::MAJ);
            return $this->redirectToRoute('parameters',[
                'id'=>$this->board->getId()]);
        }
        $vartwig=$this->menuNav->templatingadmin(
            'update',
            'parametres du panneau',
            $this->board,4);
        return $this->render('aff_websiteadmin/home.html.twig', [
            'directory'=>'parameters',
            'replacejs'=>false,
            'board'=>$this->board,
            'website'=>$this->board,
            'dispatch'=>$this->dispatch,
            'vartwig'=>$vartwig,
            'form'=>$form->createView(),
            'admin'=>[$this->admin,$this->permission],
            'city'=>$this->board->getLocality()->getCity()
        ]);
    }

    /**
     * @Route("horaires/profil-opendays/{id}", name="opendays_edit")
     * @param $id
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function editOpenDays($id): RedirectResponse|Response //todo le reôsitory $id est un website
    {
        if(!$this->getUserspwsiteOfWebsite($id) || !$this->admin )$this->redirectToRoute('cargo_public');
        $tabunique="";
        $tabconges="";
        if($opendays=$this->board->getTabopendays()){
            $tabunique=$opendays->getTabuniquejso()??[];
            $tabconges=$opendays->getCongesjso()??[];
        }

        $vartwig=$this->menuNav->templatingadmin(
            'openday',
            'parametres du panneau',
            $this->board,5);

        return $this->render('aff_websiteadmin/home.html.twig', [
            'vartwig'=>$vartwig,
            'directory'=>'parameters',
            'replacejs'=>false,
            'board'=>$this->board,
            'website'=>$this->board,
            'pw'=>$this->pw,
            'dispatch'=>$this->dispatch,
            'twigtbunique'=>$tabunique,
            'twigconges'=>$tabconges,
            'admin'=>[$this->admin,$this->permission],
            'city'=>$this->board->getLocality()->getCity()
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




    /*------------------------------------------------------- odl a verifier --------------------------------------/*/


    /**
     * @Route("new-profil-opendays/{id}", name="new-opendays")
     * @param $id
     * @return Response
     */
    public function newOpenDays($id): Response
    {
        if(!$this->getUserspwsiteOfWebsite($id) || !$this->admin )$this->redirectToRoute('cargo_public');
        if($opendays=$this->website->getTabopendays()){
            $tabunique=$opendays->getTabuniquejso();
            $conges=$opendays->getCongesjso();
        }else{
            $tabunique="";
            $conges="";
        };
        $vartwig=$this->menuNav->templatingadmin(
            'openday',
            'horaires',
            $this->website);
        return $this->render('aff_websiteadmin/home.html.twig', [
            'replacejs'=>false,
            'spaceweb'=>$this->pw,
            'website'=>$this->website,
            'twigtbunique'=>$tabunique,
            'pw'=>$this->pw,
            'twigconges'=>$conges,
            'vartwig'=>$vartwig,
            'directory'=>'parameters',
            'admin'=>[$this->admin,$this->permission],
            'city'=>$this->website->getLocality()->getCity()
        ]);
    }
}