<?php


namespace App\Controller\Modules;

use App\Classe\affisession;
use App\Lib\MsgAjax;
use App\Repository\Entity\WebsiteRepository;
use App\Repository\ServicesRepository;
use App\Module\Modulator;
use App\Util\DefaultModules;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/module/website/")
 * @IsGranted("ROLE_CUSTOMER")
 */

class ManagerModuleController extends AbstractController
{
    use affisession;

    /**
     * Ajout module a un website  //todo a finir mais actif
     *
     * @Route("add-module-for-website/{activ}/{id}", name="addmodule_website")
     * @param $activ
     * @param $id
     * @param ServicesRepository $servicesRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function addModulestape($activ,$id, ServicesRepository $servicesRepository): Response
    {
        if(!$this->getUserspwsiteOfWebsite($id) || !$this->admin ) return $this->redirectToRoute('spaceweb_mod', ['id'=>$id]);
        $servicestat=false;
        $service=$servicesRepository->findOneBy([
            'customer'=>$this->dispatch->getCustomer()->getId(),
            'namemodule'=>$activ
        ]);


        // test pour savoir si le customer a les droits sur ce service
        if($service && $service->getActive()) {
            // test pour savoir si le module n'est pas deja installé
            $listmodules = $this->board->getListmodules();
            foreach ($listmodules as $list) {
                if ($list->getClassmodule() === $activ) return $this->redirectToRoute('spaceweb_mod', ['id' => $id]); //todo le bon retour le service est déla actif erreur ?
            }
            $servicestat=true;
        }

            $vartwig=$this->menuNav->templatingadmin(
                'initModules',
                "activation d'un service",
                $this->board,2);

            return $this->render('aff_websiteadmin/home.html.twig', [
                'directory'=>'parameters',
                'replacejs'=>false,
                'vartwig' => $vartwig,
                'website'=>$this->board,
                'board'=>$this->board,
                'status'=>$servicestat,
                'module'=>DefaultModules::TAB_MODULES_INFO[$activ],
                'admin'=>[$this->admin,$this->permission],
                'city'=>$this->board->getLocality()->getCity()
            ]);
    }


    /**
     * init un module particulier //todo redirection vers la page board et test pour ne pas initialiser plisuer fois le meme module
     *
     * @Route("add-module-ajx", name="add_module_ajx")
     * @param Request $request
     * @param WebsiteRepository $websiteRepository
     * @param Modulator $modulator
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function AddNewsModule(Request $request, WebsiteRepository $websiteRepository, Modulator $modulator): JsonResponse
    {

        if($request->isXmlHttpRequest())
        {
            $slug=$request->request->get('slug');
            $module=$request->request->get('module');
            if(!$website= $websiteRepository->findWbQ3($slug,$this->iddispatch)) return new JsonResponse(MsgAjax::MSG_NOWB);

            $issue=$modulator->addModule($module,$website);
            return new JsonResponse($issue);
        }else{
            return new JsonResponse(MsgAjax::MSG_ERRORRQ);
        }
    }

}