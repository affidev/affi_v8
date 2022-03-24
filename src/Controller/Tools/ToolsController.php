<?php


namespace App\Controller\Tools;


use App\Classe\affisession;
use App\Entity\Customer\Customers;
use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Repository\Entity\ContactRepository;
use App\Repository\Entity\GpsRepository;
use App\Repository\Entity\WebsiteRepository;
use App\Repository\UserRepository;
use App\Service\Registration\CreatorUser;
use App\Service\SpaceWeb\SpacewebFactor;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/tools/jxrq")
 */


class ToolsController extends AbstractController
{
    use affisession;

    /**
     * @Route("/test-visitor-mail", name="test_mail_visitor")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param UserRepository $userRepository
     * @param ContactRepository $contactRepository
     * @return JsonResponse
     */
    public function tesEmailVisitor(Request $request, SerializerInterface $serializer, UserRepository $userRepository, ContactRepository $contactRepository): JsonResponse
    {

        $mail =  $request->request->get('email');
        $testuser=$userRepository->findOneBy(array('email'=> $mail));
        if($testuser!=null){
            $responseCode = 200;
            http_response_code($responseCode);
            header('Content-Type: application/json');
            return new JsonResponse(['ok' => true,'success' => "user",'email' => $mail]);
        }

        $contact = $contactRepository->findOneBy(array('emailCanonical' => $mail));
        if ($contact!=null) {
            $jasoncontact = $serializer->serialize($contact, 'json');
            $responseCode = 200;
            http_response_code($responseCode);
            header('Content-Type: application/json');
            return new JsonResponse(['ok' => true,'success' => "contact",'email' => $mail, 'contact' => $jasoncontact]);
        }
        return new JsonResponse(['ok' => true,'success' => "nomail", 'email' => $mail]);
    }

    /**
     * @Route("/testContactMail", options={"expose"=true}, name="test_mail") //todo old avant rajout de la fonction précedenet (30/10/2021)  rajouter une clé de sécurité
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function tesEmailContact(Request $request, SerializerInterface $serializer,UserRepository $userRepository, ContactRepository $contactRepository): JsonResponse
    {

        $data = json_decode((string) $request->getContent(), true);
        $mail=$data['email'];

        $testuser=$userRepository->findOneBy(array('email'=> $mail));
        if($testuser!=null){
            $responseCode = 200;
            http_response_code($responseCode);
            header('Content-Type: application/json');
            return new JsonResponse(['ok' => true,'success' => "user",'email' => $mail]);
        }

        $contact = $contactRepository->findOneBy(array('emailCanonical' => $mail));
        if ($contact!=null) {
            $jasoncontact = $serializer->serialize($contact, 'json');
            $responseCode = 200;
            http_response_code($responseCode);
            header('Content-Type: application/json');
            return new JsonResponse(['ok' => true,'success' => "contact",'email' => $mail, 'contact' => $jasoncontact]);
        }
        return new JsonResponse(['ok' => true,'success' => "nomail", 'email' => $mail]);
    }


    /**
     * @Route("/add-member-contactmail", options={"expose"=true}, name="add_member_contactmail")
     * @param Request $request
     * @param WebsiteRepository $websiteRepository
     * @param ContactRepository $contactRepository
     * @param SpacewebFactor $factor
     * @param CreatorUser $creatorUser
     * @param SerializerInterface $serializer
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    public function addMemberContact(Request $request,WebsiteRepository $websiteRepository, ContactRepository $contactRepository,
                                     SpacewebFactor $factor,CreatorUser $creatorUser, SerializerInterface $serializer){
        if($request->isXmlHttpRequest())
        {
            $id=$request->request->get('id');
            if(!$id==null){
                $contact=$contactRepository->find($id);
            }
            $idwebsite=$request->request->get('idwebsite');

            if(!$idwebsite==null){
                $website=$websiteRepository->find($idwebsite);
                if($website){
                    $tabmember=[
                        "contact"=>$contact??null,
                        "type"=>true,
                        "website"=>$website,
                        "mail"=>$request->request->get('mail'),
                        "pass"=>$request->request->get('pass'),
                        "name"=>$request->request->get('name'),
                        "charte"=>$request->request->get('charte')];

                    //todo rajouter un token
                    if($typeregister=$request->request->get('typeregister')=="shop"){
                        /** @var Customers $customer */
                        $customer=$creatorUser->createUserByShop($tabmember);
                        if($customer){
                            $responseCode = 200;
                            http_response_code($responseCode);
                            header('Content-Type: application/json');
                            return new JsonResponse(['success' => true, 'id' => $customer->getId(),'name'=>$customer->getProfil()->getEmailfirst()]);
                        }else {
                            return new JsonResponse(['success' => false]);
                        }
                    }else{
                        /** @var DispatchSpaceWeb $dispatch */
                        $dispatch=$factor->addwebsiteclient($tabmember);
                        if ( $dispatch) {
                            $responseCode = 200;
                            http_response_code($responseCode);
                            header('Content-Type: application/json');
                            return new JsonResponse(['success' => true, 'id' => $dispatch->getId(),'name'=>$dispatch->getName()]);
                        }else {
                            return new JsonResponse(['success' => false]);
                        }
                    }
                }
            }
        }
        return new JsonResponse(['success'=>"erreur"]);
    }


    /**
     * @Route("/reinitslug", options={"expose"=true}, name="reinitslug") // todo rajouter une clé de sécurité
     * @param GpsRepository $gpsrepo
     * @return RedirectResponse|Response
     */
    public function reinitSlug(GpsRepository $gpsrepo){
        if($this->isGranted("ROLE_SUPER_ADMIN")){
            $gps=$gpsrepo->findAll();
            foreach ($gps as $gp){
                $gp->setCity(strtolower($gp->getCity()));
                $this->em->persist($gp);
                $this->em->flush();
            }
        }
        return $this->redirectToRoute('cargo_public');
    }
}