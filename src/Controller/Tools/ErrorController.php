<?php


namespace App\Controller\Tools;

use App\Classe\affisession;
use App\Lib\Links;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tools/error")
 */

class ErrorController extends AbstractController
{
    use affisession;

    /**
     * @Route("/erreur_requete/{err}", name="api-error")
     * @param $err
     * @return Response
     */
    public function errorApi($err)
    {
        switch ($err){
            case 1:
                $msg="la clé de vérification n'est pas valide";
                break;

            default:
                $msg="erreur requete ajax";
                break;
        }
        $vartwig=$this->menuNav->templateControl(
            null,
            Links::CUSTOMER_LIST,
            'main_public/api/exception/msg',
            "",
            "messager");

        return $this->render('public/page/home.html.twig', [
            'agent'=>$this->useragent,
            'vartwig'=>$vartwig,
            "msg"=>$msg,
            'admin'=>[$this->admin,$this->permission]
        ]);
    }
}