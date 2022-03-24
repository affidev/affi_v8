<?php



namespace App\Controller\Website;

use App\Classe\affisession;
use App\Entity\DispatchSpace\Spwsite;
use App\Entity\Sector\Adresses;
use App\Entity\Sector\Gps;
use App\Entity\Sector\Sectors;
use App\Entity\Websites\Template;
use App\Entity\Websites\Website;
use App\Event\WebsiteCreatedEvent;
use App\EventSubscriber\IndexWebsiteSubscriber;
use App\Exeption\RedirectException;
use App\Lib\MsgAjax;
use App\Repository\Entity\ContactRepository;
use App\Repository\Entity\WebsiteRepository;
use App\Repository\UserRepository;
use App\Service\Localisation\LocalisationServices;
use App\Service\Modules\Evenator;
use App\Service\Search\Listpublications;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @Route("/partners/wb/event")
 * @IsGranted("ROLE_CUSTOMER")
 */

class ApiWebsiteController extends AbstractController
{
    use affisession;


    /**
     * Create a news.
     *
     * @Route("/suggest-website-ajx", options={"expose"=true}, name="suggest_website_ajx")
     * @param Request $request
     * @param LocalisationServices $locate
     * @param EventDispatcherInterface $dispatcher
     * @param WebsiteRepository $websiteRepository
     * @param UserRepository $userRepository
     * @param ContactRepository $contactRepository
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function SuggestWebsSite(Request $request,LocalisationServices $locate,EventDispatcherInterface $dispatcher, WebsiteRepository $websiteRepository, UserRepository $userRepository, ContactRepository $contactRepository): JsonResponse
    {

        if($request->isXmlHttpRequest())
        {

            $data = json_decode((string) $request->getContent(), true);
            $wb=$websiteRepository->findOneBy(['namewebsite'=>$data['name']]);
            $website=new Website();
            $template=new Template();
            $website->setTemplate($template);
            $website->setNamewebsite($data['name']);
            $website->setAttached(false);
            $testuser=$userRepository->findOneBy(array('email'=> $data['email']));
            $contact = $contactRepository->findOneBy(array('emailCanonical' => $data['email']));
            if(!$testuser && !$contact){

            }
            $tete=$tutu;
            /*  supprimer car remplacé par le Attached état
            $pw=New Spwsite();
            $pw->setDisptachwebsite($this->getDispatch());
            $pw->setRole("member");
            $pw->setIsadmin(false);
            $website->addSpwsite($pw);
            */
            if($data['ville']!=""){
                $gps = $locate->findLocate($data['ville']);
                if($gps==null) return new JsonResponse(['success'=> false ]);
                $website->setLocality($gps);
            }

            $this->em->persist($website);
            $this->em->flush();

           // $json = $normalizer->normalize($website,null,['groups' => 'edit_event']);
            $event= new WebsiteCreatedEvent($website);
            $dispatcher->dispatch($event, WebsiteCreatedEvent::CREATE);
            return new JsonResponse(true);
        }else{
            return new JsonResponse(MsgAjax::MSG_ERRORRQ);
        }
    }

    /**
     *
     * @Route("/oldadd-website-ajx", options={"expose"=true}, name="oldadd_website_ajx")
     * @param Request $request
     * @param LocalisationServices $locate
     * @param EventDispatcherInterface $dispatcher
     * @param NormalizerInterface $normalizer
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function oldAddWebsiteAjx(Request $request,LocalisationServices $locate,EventDispatcherInterface $dispatcher, NormalizerInterface $normalizer,): JsonResponse
    {

        if($request->isXmlHttpRequest())
        {

            $data = json_decode((string) $request->getContent(), true);
            dump($data);
            $website=new Website();
            $template=new Template();
            $website->setTemplate($template);
            $website->setNamewebsite($data['name']);
            $website->setAttached(false);
            $tete=$tutu;
            /*  supprimer car remplacé par le Attached état
            $pw=New Spwsite();
            $pw->setDisptachwebsite($this->getDispatch());
            $pw->setRole("member");
            $pw->setIsadmin(false);
            $website->addSpwsite($pw);
            */
            if($data['code']!=""){
                $sector=new Sectors();
                $adresse=new Adresses();
                $gps = $locate->defineGpsOfNewWebsite($data['code'], $data['city']);
                if($gps==null) return new JsonResponse(['success'=> false ]);
                $website->setLocality($gps);
                $adresse->setCodePostal($data['code']);
                $adresse->setNomCommune($data['city']);
                $adresse->setNumero($data['number']);
                $adresse->setNomVoie($data['street']);
                $sector->addAdresse($adresse);
                $template->setSector($sector);
                $this->em->persist($sector);
                $this->em->persist($adresse);
                $this->em->persist($gps);
            }

            $this->em->persist($website);
            $this->em->flush();

            $json = $normalizer->normalize($website,null,['groups' => 'edit_event']);
            $event= new WebsiteCreatedEvent($website);
            $dispatcher->dispatch($event, WebsiteCreatedEvent::CREATE);
            return new JsonResponse($json);
        }else{
            return new JsonResponse(MsgAjax::MSG_ERRORRQ);
        }
    }
}