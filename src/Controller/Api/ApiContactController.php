<?php


namespace App\Controller\Api;

use App\Classe\affisession;
use App\Entity\Module\Contactation;
use App\Entity\Websites\Website;
use App\Lib\Links;
use App\Repository\Entity\WebsiteRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



/**
 * @Route("/msg/form")
 */

class ApiContactController extends AbstractController
{
    use affisession;

    /**
     * @Route("contact/{slug}/{key}", options={"expose"=true}, name="contact_module_spwb", requirements={"slug":"[a-z0-9\-]*"})
     * @param WebsiteRepository $websiteRepository
     * @param Session $session
     * @param $slug
     * @param $key
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function newMessageContact(WebsiteRepository $websiteRepository,Session $session,$slug,$key): RedirectResponse|Response
    {

        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $uri=$_SERVER['REQUEST_URI'];
        if(isset($_SERVER['HTTP_REFERER'])){
                $ref=$_SERVER['HTTP_REFERER'];
        }else{
            $ref='none';
        }

       $session->set('tabinfoip',['ip'=>$ip, 'uri'=>$uri,'ref'=>$ref]);

        /** @var Website $website */
        if (!$website=$websiteRepository->findWbBySlug($slug))return $this->redirectToRoute('api-error', ['err' => 1]);
        /** @var Contactation $contactation */
        if($website->getContactation()->getKeycontactation()!==$key)return $this->redirectToRoute('api-error', ['err' => 1]);
        return $this->redirectToRoute('message_comeout_website',['key' =>$website->getCodesite()]);
    }



    /**
     * @Route("/home-contact/{spaceweb}/{contact}", name="confirm") //todo revoir ce retour
     * @param $spaceWeb
     * @param null $contact
     * @param null $id
     * @return Response
     */
    public function reponseApi($spaceWeb, $contact=null, $id=null): Response
    {

        if($this->isGranted("ROLE_CUSTOMER")){
            $this->addFlash('newmsg', 'votre message a bien été adressé à : '.$spaceWeb.'.');
            return $this->redirectToRoute('index_customer',[],302);
        }else {
            $this->addFlash('newmsg', 'Merci '.$contact.' , votre message a bien été adressé à : '.$spaceWeb.'.');
            $vartwig = $this->menuNav->templateControl(
                null,
                Links::CUSTOMER_LIST,
                'customer/reponse',
                "customer",
                'customer');
            return $this->redirectToRoute('confirm-flash', [], 302);
        }
    }

    /**
     * @Route("/register-contact/{id}", name="app_register") //todo revoir ce retour
     * @return Response
     */
    public function registerApi($id): Response
    {
            $vartwig = $this->menuNav->templateControl(
                null,
                Links::CUSTOMER_LIST,
                'customer/reponse',
                "customer",
                'contact');
            return $this->redirectToRoute('confirm-flash', [], 302);

    }
/*
    /**
     * @Route("old-contact/{slug}/{key}", options={"expose"=true}, name="old_contact_module_spwb", requirements={"slug":"[a-z0-9\-]*"})
     * @param MsgWebisteRepository $msgWebisteRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param Environment $twig
     * @param ContactRepository $contactRepository
     * @param SpacewebFactor $factor
     * @param Messageor $messageor
     * @param DispatchSpaceWebRepository $dispatchSpaceWebRepository
     * @param ContactationRepository $contactationRepository
     * @param $slug
     * @param $key
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    /*
    public function oldnewMessageContact(MsgWebisteRepository $msgWebisteRepository,PaginatorInterface $paginator, Request $request, Environment $twig,ContactRepository $contactRepository, SpacewebFactor $factor, Messageor $messageor,
                                         DispatchSpaceWebRepository $dispatchSpaceWebRepository, ContactationRepository $contactationRepository, $slug,$key): RedirectResponse|Response
    {
        $msgs=[];

        if(!$this->iddispatch) {
            $contactation = $contactationRepository->findMsgByKey($key);
            if (!$contactation) return $this->redirectToRoute('api-error', ['err' => 1]);
            $website = $contactation->getModuletype()->getWebsite();
            $user = false;
            $member=false;
        }else{
            if(!$pw=$this->getUserspwsiteOfWebsiteSlug($slug)){ //todo le rajout de la question : voulez ajoutez rejoindre ce website, ou juste laissez un message ???
                $contactation=$contactationRepository->findMsgByKey($key);
                if(!$contactation)return $this->redirectToRoute('api-error',['err'=>1]);
                $website=$contactation->getModuletype()->getWebsite();
                $user=true;
                $member=false;
            }else{
                $website = $pw->getWebsite();
                if (!$pw->getWebsite()->getModule()->getContactation()->getKeycontactation() == $key) return $this->redirectToRoute('api-error', ['err' => 1]);
                $pagination=$paginator->paginate(
                    $msgWebisteRepository->findMsgswebsiteQuery($website->getId()),
                    $request->query->getInt('page', 1),
                    10
                );


                $msgs = $messageor->sortmesswebsite($pagination,$pw);
                $user = true;
                $member = true;
            }
        }
        $messageWb = new MsgWebsite();
        $form=$this->createForm(ContactFormType::class, $messageWb);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if(!$user){
                switch ($form['type']->getData()) {
                    case 'member':
                        $idmember=$form['id']->getData();
                        $dispatchexpe=$dispatchSpaceWebRepository->find($idmember);
                        break;
                    case 'contact':
                        $idcontact=$form['id']->getData();
                        $contact=$contactRepository->find($idcontact);
                        break;
                    case 'new':
                        $contact=null;
                        break;
                    default:
                        break;
                }
            }else{
                $dispatchexpe=$this->getUserDispatch(); //dans le cas user mais pas membre du website avec option je veux rejoindre ce webite
                if(!$member && $idcontact=$form['follow']->getData()=="oui"){
                    $factor->addSpwsiteClient($website, $dispatchexpe);
                }
            }
            $messageor->newMessage([
                'form'=>$form,
                'messagewb'=> $messageWb,
                'website'=> $website,
                'dispatch'=>$dispatchexpe??null,
                'contact'=>$contact ?? null,
            ]);
            if($user){
                return $this->redirectToRoute('contact_module_spwb',['slug'=>$website->getSlug(),'key'=>$key]);
            }else{
                $this->addFlash('newmsg', ' votre message a bien été adressé à : '.$website->getNamewebsite().'.');
                return $this->redirectToRoute('contact_keep',['slug'=>$website->getSlug(),'type'=>$form['type']->getData(),'id'=>$form['id']->getData()]);
            }
        }
        $vartwig=$this->dispatch->dispatchinfo( $website,'conversations/conversation','conversations', $member);
        return $this->render('website/home.html.twig', [
            'agent'=>$this->useragent,
            'form' => $form->createView(),
            'website'=>$website,
            'msgs'=>$msgs,
            'key'=>$key,
            'vartwig'=>$vartwig,
            'pw'=>$this->pw??['role'=>'visitor'],
            'member'=>$member,
            'pagination'=>$pagination??null,
            "user"=>$user,
            'directory'=>'public',
            'admin'=>[$this->admin,$this->permission]
        ]);
    }

*/
}