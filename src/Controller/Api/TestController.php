<?php


namespace App\Controller\Api;


use App\Classe\affisession;
use App\Lib\MsgAjax;
use App\Lib\Tools;
use App\Repository\Entity\GpsRepository;
use App\Repository\Entity\WebsiteRepository;
use App\Service\Localisation\GpsServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("api/jxrq")
 */



class TestController extends AbstractController
{
    use affisession;

    /**
     * @Route("/testgps", name="test_gps")
     * @param Request $request
     * @param GpsRepository $gpsRepository
     * @param GpsServices $gpsServices
     * @return JsonResponse
     */
    public function tesGps(Request $request, GpsRepository $gpsRepository, GpsServices $gpsServices): JsonResponse
    {
        if($request->isXmlHttpRequest())
        {
        $city = json_decode((string) $request->getContent(), true); //retour de la requete gouv.ville
        if (!$this->dispatch) return new JsonResponse(['ok' => false, 'success' => "dispatch introuvable"]);
        $test = false;
        $gps = $gpsRepository->findOneBy(['insee' => $city['code']]);
        if (!$gps) {
            $gps = $gpsServices->newGpsLocate($city);
            $create=true;
            $result = $gps->getId();
        } else {
            $create=false;
            $result = $gps->getId();
            if(!$this->iddispatch==1) { // permet au superadmin de créer des panneaux sur une même localité
                foreach ($this->dispatch->getSpwsite() as $pw) {
                    if ($pw->getWebsite()->getLocality()->getId() == $gps->getId()) {
                        $test = true;
                        $website = [
                            'name' => $pw->getWebsite()->getNamewebsite(),
                            'id' => $pw->getWebsite()->getId()];
                        break;
                    }
                }
            }
        }
        $responseCode = 200;
        http_response_code($responseCode);
        header('Content-Type: application/json');
        return new JsonResponse(['success' => $test,'create'=>$create, 'result' => $result,'website'=>$website??[]]);
        }else{
            return new JsonResponse(MsgAjax::MSG_ERRORRQ);
        }
    }

    /**
     * @Route("/testwebsite", name="test_website")
     * @param Request $request
     * @param WebsiteRepository $websiteRepository
     * @return JsonResponse
     */
    public function tesWebsite(Request $request, WebsiteRepository $websiteRepository): JsonResponse
    {
        if($request->isXmlHttpRequest())
        {
            $name = json_decode((string) $request->getContent(), true); //retour du nom
            if (!$this->dispatch) return new JsonResponse(['ok' => false, 'success' => "dispatch introuvable"]);
            $string=Tools::clean($name[0]);

                $website = $websiteRepository->ifExistName($string);
                if ($website) {
                    $test = true;
                } else {
                    $test = false;
                }

            $responseCode = 200;
            http_response_code($responseCode);
            header('Content-Type: application/json');
            return new JsonResponse(['success' => $test,'name'=>$string]);

        }else{
            return new JsonResponse(MsgAjax::MSG_ERRORRQ);
        }
    }

}