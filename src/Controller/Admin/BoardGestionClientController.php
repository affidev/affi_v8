<?php


namespace App\Controller\Admin;


use App\Classe\adminsession;
use App\Entity\Admin\Orders;
use App\Entity\Customer\Customers;
use App\Form\OrderType;
use App\Repository\Entity\CustomersRepository;
use App\Repository\Entity\DispatchSpaceWebRepository;
use App\Repository\Entity\SpwsiteRepository;
use App\Repository\Entity\WebsiteRepository;
use App\Service\Gestion\Commandar;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/board-v5-1/gest-customer/")
 * @IsGranted("ROLE_SUPER_ADMIN")
 */

class BoardGestionClientController extends AbstractController
{

    use adminsession;

    /**
     * @Route("customer/{keyboard}/{id}", name="back_admin_gest_customer")
     * @param $keyboard
     * @param $id
     * @param SpwsiteRepository $spwsiteRepository
     * @param WebsiteRepository $websiteRepository
     * @param CustomersRepository $customersRepository
     * @param DispatchSpaceWebRepository $dispatchSpaceWebRepository
     * @return Response
     */
    public function tabCustomerBo($keyboard, $id, SpwsiteRepository $spwsiteRepository, WebsiteRepository $websiteRepository,CustomersRepository $customersRepository, DispatchSpaceWebRepository $dispatchSpaceWebRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        if($keyboard!=$this->getParameter('key_admin')) $this->redirectToRoute('app_logout');

        /** @var Customers $customer */
        $customer=$customersRepository->allInfo($id);
        $vartwig=[
            "title"=>'backoffice',
            "description"=>'backoffice',
            "keyword"=>'backoffice',
            'page'=>"customer",
            "tagueries"=>[["name"=> "backinfo website"]],
            ];
        return $this->render('backoffice/customer/customer.html.twig', [
            'customer'=>$customer[0],
            'vartwig'=>$vartwig
        ]);
    }

    /**
     * @Route("cmd-customer/{id}", name="back_admin_cmd_customer")
     * @param $id
     * @param Request $request
     * @param Commandar $commandar
     * @param SpwsiteRepository $spwsiteRepository
     * @param WebsiteRepository $websiteRepository
     * @param CustomersRepository $customersRepository
     * @param DispatchSpaceWebRepository $dispatchSpaceWebRepository
     * @return Response
     */
    public function newCmdCustomer($id, Request $request, Commandar $commandar ,SpwsiteRepository $spwsiteRepository, WebsiteRepository $websiteRepository,CustomersRepository $customersRepository, DispatchSpaceWebRepository $dispatchSpaceWebRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $website=$websiteRepository->find(3);
        $dispatch=$dispatchSpaceWebRepository->find(1);
        $pw=$spwsiteRepository->find(1);
        /** @var Customers $customer */
        $customer=$customersRepository->allInfo($id);
        $order =New Orders();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandar->calCmd($order);
        }
        $vartwig=[
            "title"=>'backoffice',
            "description"=>'backoffice',
            "keyword"=>'backoffice',
            'page'=>"adorder",
            "tagueries"=>[["name"=> "backinfo website"]],
        ];
        return $this->render('backoffice/product/adorder.html.twig', [
            'form' => $form->createView(),
            'website'=>$website,
            'pw'>$pw,
            'dispatch'=>$dispatch,
            'customer'=>$customer[0],
            'vartwig'=>$vartwig
        ]);
    }

    /**
     * @Route("cmd-blk-customer/{id}", name="back_admin_cmd_blk_customer")
     * @param $id
     * @param Request $request
     * @param Commandar $commandar
     * @param SpwsiteRepository $spwsiteRepository
     * @param WebsiteRepository $websiteRepository
     * @param CustomersRepository $customersRepository
     * @param DispatchSpaceWebRepository $dispatchSpaceWebRepository
     * @return Response
     */
    public function newBlokCmdCustomer($id, Request $request, Commandar $commandar ,SpwsiteRepository $spwsiteRepository, WebsiteRepository $websiteRepository,CustomersRepository $customersRepository, DispatchSpaceWebRepository $dispatchSpaceWebRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $website=$websiteRepository->find(3);
        $dispatch=$dispatchSpaceWebRepository->find(1);
        $pw=$spwsiteRepository->find(1);
        /** @var Customers $customer */
        $customer=$customersRepository->allInfo($id);

        $vartwig=[
            "title"=>'backoffice',
            "description"=>'backoffice',
            "keyword"=>'backoffice',
            "tagueries"=>[["name"=> "backinfo website"]],
        ];
        return $this->render('backoffice/customer/orders.html.twig', [
            'website'=>$website,
            'pw'>$pw,
            'dispatch'=>$dispatch,
            'customer'=>$customer[0],
            'vartwig'=>$vartwig
        ]);
    }
}