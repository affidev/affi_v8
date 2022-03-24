<?php


namespace App\Controller\Customer;


use App\Classe\affisession;
use App\Lib\Links;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/customer/services/directive/")
 * @IsGranted("ROLE_CUSTOMER")
 */

class ServicesCustomerController extends AbstractController
{
    use affisession;

    /**
     * @Route("list-module", name="list_module_customer")
     * @return Response
     */
    public function listModuleCustomer(): Response
    {
        $tabservice=[];
        if(!$this->dispatch) return $this->redirectToRoute('cargo_public');
        $this->activeBoard();

        $listservices=$this->dispatch->getCustomer()->getServices();

        foreach ($listservices as $list) {
            $tabservice[]=$list->getNamemodule();
        }

        $vartwig=$this->menuNav->newtemplateControlCustomer(
            Links::CUSTOMER_LIST,
            'modules',
            "services actifs",
            5);

        return $this->render('aff_account/home.html.twig', [
            'directory'=>'profil',
            'replacejs'=>$replace??null,
            'board'=>$this->board,
            'services'=>$tabservice,
            'dispatch'=>$this->dispatch,
            'vartwig'=>$vartwig,
            'permissions'=>$this->permission,
        ]);
    }
}