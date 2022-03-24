<?php


namespace App\Controller\Marketplace;


use App\Classe\affisession;
use App\Entity\LogMessages\MsgW;
use App\Entity\LogMessages\PrivateConvers;
use App\Entity\Marketplace\Offres;
use App\Entity\Websites\Website;
use App\Form\ContactFormType;
use App\Form\PrivateConversFormType;
use App\Lib\Links;
use App\Repository\Entity\ContactationRepository;
use App\Repository\Entity\ContactRepository;
use App\Repository\Entity\DispatchSpaceWebRepository;
use App\Repository\Entity\MsgWebisteRepository;
use App\Repository\Entity\OffresRepository;
use App\Repository\Entity\WebsiteRepository;
use App\Service\Messages\Messageor;
use App\Service\Messages\PrivateMessageor;
use App\Service\SpaceWeb\SpacewebFactor;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;


/**
 * @Route("/shop/panier/order/")
 */
class ApiMarketController extends AbstractController
{
    use affisession;

    /**
     * @Route("new-option/{id}", options={"expose"=true}, name="new_option_market") // option d'un customer
     * @param OffresRepository $offresRepository
     * @param PrivateMessageor $privateMessageor
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    public function newOptionMarket(OffresRepository $offresRepository,PrivateMessageor $privateMessageor, Request $request, $id)
    {

        $this->denyAccessUnlessGranted('ROLE_CUSTOMER');
        if(!$dispatch=$this->userdispatch) return $this->redirectToRoute('api-error',['err'=>1]);
        $customer=$dispatch->getCustomer();

        /** @var Offres $offre */
        $offre=$offresRepository->findOffreAndWebsite($id);
        if(!$offre) return $this->redirectToRoute('api-error', ['err' => 1]);
        $website=$offre->getWebsite();
        $convers= new PrivateConvers();
        $form=$this->createForm(PrivateConversFormType::class, $convers);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $privateMessageor->newOptionMarket([
                'form'=>$form,
                'convers'=> $convers,
                'website'=> $website,
                'offre'=>$offre,
                'client'=>$this->getUserDispatch(),
            ],true);

            return $this->redirectToRoute('think_market',['slug'=>$website->getSlug()]);
        }

        return $this->render('aff_customer/home.html.twig', [
            'form' => $form->createView(),
            'replacejs'=>$replace??null,
            'customer'=>$customer,
            'dispatch'=>$dispatch,
            'website'=>$website,
            'vartwig'=>$this->dispatch->dispatchinfo( $website,'privateconversToBuy','market', null),
            'offre' =>$offre,
            "user"=>true,
            'directory'=>'privateconvers',
            'admin'=>[$this->admin,$this->permission]

        ]);
    }

    /**
     * @Route("option-market/{id}", name="pre_option_market")
     * @param ContactRepository $contactRepository
     * @param OffresRepository $offresRepository
     * @param DispatchSpaceWebRepository $dispatchSpaceWebRepository
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function prepareNewOptionMarket(ContactRepository  $contactRepository,OffresRepository $offresRepository, DispatchSpaceWebRepository $dispatchSpaceWebRepository, Request $request, $id)
    {
        /** @var Offres $offre */
        $offre=$offresRepository->findOffreAndWebsite($id);
        if(!$offre) return $this->redirectToRoute('api-error', ['err' => 1]);
        /** @var Website $website */
        $website=$offre->getWebsite();
        $locate=$website->getLocality();
        $city=$locate->getSlugcity();
        $convers= new PrivateConvers();
        $form=$this->createForm(PrivateConversFormType::class, $convers);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $dispatch=$form['id'];
            if(!$this->iddispatch) {
                $user = false;
                $member=false;
            }else {
                $user=true;
               $member=false;

            }
            if(!$user) {
                switch ($form['type']->getData()) {
                    case 'member':
                        $idmember = $form['id']->getData();
                        $dispatchexpe = $dispatchSpaceWebRepository->find($idmember);
                        break;
                    case 'contact':
                        $idcontact = $form['id']->getData();
                        $contact = $contactRepository->find($idcontact);
                        break;
                    case 'new':
                        $contact = null;
                        break;
                    default:
                        break;
                }
            }

            $marketor->newOption([
                'form'=>$form,
                'messagewb'=> $convers,
                'website'=> $website,
                'autor'=>$offre->getAuthor(),
                'client'=>$dispatchexpe??null,
               // 'transac'=>$transac
            ]);

        }
        $vartwig=$this->dispatch->dispatchinfo( $website,'buyidentification','market', null);
        return $this->render('aff_website/home.html.twig', [
            'replacejs'=>$replace??null,
            'form' => $form->createView(),
            'offre'=>$offre,
            'op'=>false,
            'website'=>$website,
            'vartwig'=>$vartwig,
            'pw'=>$this->pw??['role'=>'visitor'],
            'member'=>null,
            'pagination'=>$pagination??null,
            "user"=>null,
            'directory'=>'market',
            'city'=>$city,
            'admin'=>[$this->admin,$this->permission]
        ]);
    }

    /**
     * @Route("commande-market/{id}", options={"expose"=true}, name="commande_market")
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
    public function newCmdMarket(MsgWebisteRepository $msgWebisteRepository,PaginatorInterface $paginator, Request $request, Environment $twig,ContactRepository $contactRepository, SpacewebFactor $factor, Messageor $messageor,
                                      DispatchSpaceWebRepository $dispatchSpaceWebRepository, ContactationRepository $contactationRepository, $slug,$key){
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

                /** @var PaginatorInterface $pagination */
                $msgs = $messageor->sortmesswebsite($pagination,$pw);
                $user = true;
                $member = true;
            }
        }
        $messageWb = new MsgW();
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
            'replacejs'=>$replace??null,
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

    /**
     * @Route("/confirmation-convers-market{slug}", name="think_market")
     * @param WebsiteRepository $websiteRepository
     * @param null $slug
     * @return Response
     */
    public function confirmPrivateConvers(WebsiteRepository $websiteRepository,$slug): Response
    {

        if(!$dispatch=$this->userdispatch) return $this->redirectToRoute('api-error',['err'=>1]);
        $customer=$dispatch->getCustomer();

        $vartwig=$this->dispatch->templateControl(
            Links::CUSTOMER_LIST,
            'confirmprivatemsg',
            "confirmation",
            'all');

        return $this->render('aff_customer/home.html.twig', [
            'replacejs'=>$replace??null,
            'vartwig'=>$vartwig,
            'customer'=>$customer,
            'dispatch'=>$dispatch,
            'directory'=>'privateconvers',
            'admin'=>[$this->admin,$this->permission]
        ]);
    }

}