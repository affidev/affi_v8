<?php


namespace App\Controller\Bulle;


use App\Classe\affisession;
use App\Entity\Bulles\Bulle;
use App\Repository\Entity\BulleRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/bulle/")
 */
class MainBulleController extends AbstractController
{
    use affisession;

    /**
     * @Route("old-show-bubble/{id}", name="old_show_bubble")
     * @param $id
     * @param BulleRepository $bulleRepository
     * @param Session $session
     * @return Response
     * @throws NonUniqueResultException
     */
    public function oldshowBubble($id, BulleRepository $bulleRepository, Session $session)
    {
        /** @var Bulle $bubble */
        $bubble=$bulleRepository->findAllOneBUlle($id);
        if(!$bubble)throw $this->createNotFoundException("La bulle n'existe pas");
        $website=$bubble->getModuletype()->getWebsite();
       /* if($this->isGranted('IS_AUTHENTICATED_FULLY')){
            $typeUser="spaceweb";
            $dispatchspace=$session->get('iddisptachweb');
            $catching=new Catching();
            $catching->setSpacewebcatch($dispatchspace);
            $bubble->addCatching($catching);
            $this->em->persist($catching);
        }else{
            $typeUser="visiteur";
        }*/
        $bubble->increavue();
        $this->em->flush();
        return $this->render('layout/layout_showbuble.html.twig', [
            'agent'=>$this->useragent,
            'vartwig' => $this->dispatch->bulleInfo($bubble, $website,'bulle/show',"bulle"),
            'website' =>  $website,
            'pwsite'=>['isadmin'=>false],
            'bulle' => $bubble,
            'admin'=>$this->admin
        ]);
    }

    /**
     * @Route("show-bubble/{id}", options={"expose"=true}, name="show_bubble")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param $id
     * @param BulleRepository $bulleRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function showBubble(Request $request, SerializerInterface $serializer, $id, BulleRepository $bulleRepository)
    {
        if($request->isXmlHttpRequest())
        {
            $bubble=$bulleRepository->findShowBulle($id);
            if($bubble!=null){
                $bubble->increavue();
                $this->em->flush();
                /* if($this->isGranted('IS_AUTHENTICATED_FULLY')){
                     $typeUser="spaceweb";
                     $dispatchspace=$session->get('iddisptachweb');
                     $catching=new Catching();
                     $catching->setSpacewebcatch($dispatchspace);
                     $bubble->addCatching($catching);
                     $this->em->persist($catching);
                 }else{
                     $typeUser="visiteur";
                 }*/
                $jasonbulle = $serializer->serialize($bubble, 'json',['groups' => 'buble']);
                $responseCode = 200;
                http_response_code($responseCode);
                header('Content-Type: application/json');
                return new JsonResponse(['succes' => "true", "bulle"=>$jasonbulle]);
            }

            return new JsonResponse(['succes' => "false"]);
        }
        return new JsonResponse(['succes'=>"false"]);
    }

    /**
     * @Route("show-simple-bubble/{id}", options={"expose"=true}, name="show_simple_bubble")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param $id
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function showSimpleBubble(Request $request, SerializerInterface $serializer, BulleRepository $bulleRepository,$id)
    {
        if($request->isXmlHttpRequest())
        {
            $bubble=$bulleRepository->findShowBulle($id);
            if($bubble!=null){
                $bubble->increavue();
                $this->em->flush();
                $jasonbulle = $serializer->serialize($bubble, 'json',['groups' => 'simplebuble']);
                $responseCode = 200;
                http_response_code($responseCode);
                header('Content-Type: application/json');
                return new JsonResponse(['succes' => "true", "bulle"=>$jasonbulle]);
            }
            return new JsonResponse(['succes' => "false"]);
        }
        return new JsonResponse(['succes'=>"false"]);
    }
}