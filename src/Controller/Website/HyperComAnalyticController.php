<?php


namespace App\Controller\Website;

use App\Classe\affisession;
use App\Entity\HyperCom\TagAnalytic;
use App\Repository\Entity\WebsiteRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route("/hypercomanalytic/")
 */

class HyperComAnalyticController extends AbstractController
{
    use affisession;

    /**
     * @Route("/clic/", name="clic_tag")
     * @param Request $request
     * @param WebsiteRepository $repowebsite
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function Tagclic(Request $request, WebsiteRepository $repowebsite, UserPasswordEncoderInterface $passwordEncoder){

        if(!$request->isXmlHttpRequest()){
            $submit=$request->request->all();
            $wb=$submit['website'];
            $key=$submit['key']; //le code secret du site appellant (variable twig app_wbstite)
            $tag=$submit['tag'];
            $website=$repowebsite->findWebsiteCodesite($key);
            if($website->getId()==$wb){
                if($tag!=null){
                    $clic=new TagAnalytic();
                    $clic->setWebsite($website);
                    $clic->setTagname($tag);
                    $this->em->persist($clic);
                    $this->em->flush();
                    $response = new JsonResponse();
                    $response->setData(['succes' => true ]);
                    return $response;
                }
            }
        }
        $response = new JsonResponse();
        $response->setData(['succes' => false, 'error'=>'requete pas acceptée']);
        return $response;
    }

}