<?php


namespace App\Controller\Modules;


use App\Classe\affisession;
use App\Repository\Entity\ContactationRepository;
use App\Module\Modulator;
use App\Util\DefaultModules;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/module/contact")
 * @IsGranted("ROLE_CUSTOMER")
 */

class ContactController extends AbstractController
{
    use affisession;

    /**
     * @Route("/process-module-contact/{id}", options={"expose"=true}, name="process_contact")
     * @param DefaultModules $defaultModules
     * @param null $id
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function processContact(DefaultModules $defaultModules,$id): RedirectResponse|Response
    {
        if(!$this->getUserspwsiteOfWebsite($id) || !$this->admin )$this->redirectToRoute('cargo_public');

        $moduletab=$defaultModules->selectModule($this->board);

        $test1= (bool)($email = $this->board->getTemplate()->getEmailspaceweb());
        $test2= (bool)($sector = $this->board->getTemplate()->getSector());

        $vartwig=$this->menuNav->templatingadmin(
            'comonModule',
            'module de contact',
            $this->board,2);

        return $this->render('aff_websiteadmin/home.html.twig', [
            'directory'=>'parameters',
            'vartwig' => $vartwig,
            'replacejs'=>false,
            'website'=>$this->board,
            'board'=>$this->board,
            'tabmodule'=>$moduletab,
            'test1'=>$test1,
            'test2'=>$test2,
            'email'=>$email,
            'sector'=>$sector,
            'admin'=>[$this->admin,$this->permission],
            'city'=>$this->board->getLocality()->getCity()
        ]);
    }


    /**
     * @Route("/creation-module-contact/{id}", name="new_contact_mod")
     * @param Request $request
     * @param Modulator $modulator
     * @param $id
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function newModContact( Request $request, Modulator $modulator, $id): RedirectResponse|Response
    {
        if(!$this->getUserspwsiteOfWebsite($id) || !$this->admin )$this->redirectToRoute('cargo_public');

        $modulator->initContactor($this->board);

       // $this->addFlash('infoprovider', 'nouveau module de contact ok.');
        return $this->redirectToRoute('spaceweb_mod',['id'=>$this->board->getId()]);

    }

    /**
     * activation/maj du contactation
     *
     * @Route("active-contactation-for-website/{id}", name="init_contactation")
     * @param $id
     * @param Modulator $modulator
     * @return Response
     * @throws NonUniqueResultException
     */
    public function initContactation($id, Modulator $modulator): Response
    {
        if(!$this->getUserspwsiteOfWebsite($id) || !$this->admin ) return $this->redirectToRoute('spaceweb_mod', ['id'=>$id]);

        $modulator->initContactor($this->board);

        return $this->redirectToRoute('spaceweb_mod', ['id'=>$id]);
    }

/*--------------------------------------------- a controler ------------------------------------*/




    /**
     * @Route("/contacts", name="contacts_customer")
     * @param ContactationRepository $contactationRepository
     * @return Response
     * @throws \Exception
     */
    public function contactsDispatchSpace(ContactationRepository $contactationRepository): Response //todo
    {
        if(!$this->iddispatch) return $this->redirectToRoute('cargo_public');
        $contactation=$contactationRepository->findContactationByModuleAndIdProvider($this->iddispatch);
        if(!$contactation)return $this->redirectToRoute('index_customer'); //todo pas forcement faire une info pas de message par exemple
        $module=$contactation->getModuleType();

        $dispatchspace=$module->getWebsite();
        $bullesmsg=$contactation->getMessages();
        $vartwig=$this->menuNav->templatingspaceWeb(
            'main_spaceweb/message/messages',
            'Privatemsg',
            $this->website
        );
        $tabtags=[
            ['id'=>1,'name'=>"all",'active'=>true, 'link'=>'contacts_customer'],
        ];
        return $this->render('layout/layout_mainspaceweb.html.twig', [
            'vartwig'=>$vartwig,
            'msgs'=>$bullesmsg,
            'spaceweb'=>$dispatchspace,
            'tags'=>$tabtags,
            'admin'=>$this->admin
        ]);
    }


}