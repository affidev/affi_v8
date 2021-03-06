<?php


namespace App\Service\Gestion;


use App\Entity\Admin\Numeratum;
use App\Entity\Admin\OrderProducts;
use App\Entity\Admin\Orders;
use App\Entity\Admin\Products;
use App\Entity\Admin\Wbcustomers;
use App\Entity\Admin\WbOrderProducts;
use App\Entity\Admin\Wborders;
use App\Entity\Customer\Customers;
use App\Entity\Customer\CommandeFormule;
use App\Repository\Entity\OrderProductsRepository;
use App\Repository\Entity\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

class Commandar
{

    private ProductsRepository $productsRepository;
    private Numerator $numerator;
    private EntityManagerInterface $em;
    private OrderProductsRepository $orderrepo;

    public function __construct(EntityManagerInterface $em, OrderProductsRepository $orderrepo, ProductsRepository $productsRepository, Numerator $numerator)
    {
        $this->em = $em;
        $this->productsRepository = $productsRepository;
        $this->numerator = $numerator;
        $this->orderrepo=$orderrepo;
    }

    /**
     * @param $order Wborders
     * @return mixed
     */
    public function calCmdWb(Wborders $order): Wborders
    {
        $totalHT = 0;
        $totalTVA = 0;
        foreach($order->getProducts() as $orderProduct)
        {
            $produit=$orderProduct->getProduct();
            $price=$this->calPrices($orderProduct, $produit);
            $totalHT += $price['HT'];
            if (!isset($totalTVA)) {
                $totalTVA = (round($price['TTC'] - $price['HT'], 2));
            }else{
                $totalTVA += round($price['TTC'] - $price['HT'],2);
            }
            $orderProduct->setOrder($order);
        }
        $order->setTotaltva($totalTVA);
        $order->setTotalht(round($totalHT,2));
        $order->setTotalttc(round($totalHT + $totalTVA,2));
        return $order;
    }

    /**
     * @param $order Orders
     * @return Orders
     */
    public function calCmd(Orders $order): Orders
    {
        $totalHT = 0;
        $totalTVA = 0;
        foreach($order->getListproducts() as $orderproduct)
        {
            $produit = $orderproduct->getProduct();
            $price=$this->calPrices($orderproduct, $produit);
            $totalHT += $price['HT'];
            if (!isset($totalTVA)){
                $totalTVA = (round($price['TTC'] - $price['HT'], 2));
            }else{
                $totalTVA += round($price['TTC'] - $price['HT'], 2);
            }
        }
        $order->setTotaltva($totalTVA);
        $order->setTotalht(round($totalHT,2));
        $order->setTotalttc(round($totalHT + $totalTVA,2));
        return $order;
    }

    /**
     * ici on recalcul ?? la date de la facture avec les tarfs actuels
     * @param Wborders $order
     * @return Wborders
     */
    public function initfacture(Wborders $order): Wborders
    {
        $totalHT = 0;
        $totalTVA = 0;
        /** @var WbOrderProducts $orderProduct */
        foreach($order->getProducts() as $orderProduct)
        {
            $produit=$orderProduct->getProduct();
            $price=$this->calPrices($orderProduct, $produit);
            $orderProduct->setValider(true);
            $this->em->persist($orderProduct);
            $totalHT += $price['HT'];
            if (!isset($totalTVA)) {
                $totalTVA = (round($price['TTC'] - $price['HT'], 2));
            }else{
                $totalTVA += round($price['TTC'] - $price['HT'],2);
            }
        }
        $order->setTotaltva($totalTVA);
        $order->setTotalht(round($totalHT,2));
        $order->setTotalttc(round($totalHT + $totalTVA,2));
        $order->setValider(true);
        $this->em->persist($order);
        return $order;
    }

    /**
     * ici on recalcul ?? la date de la facture avec les tarfs actuels
     * @param Orders $order
     * @return Orders
     */
    public function initfactureCustomer(Orders $order): Orders
    {
        $totalHT = 0;
        $totalTVA = 0;
        foreach($order->getListproducts() as $orderProduct)
        {
            $produit=$orderProduct->getProduct();
            $price=$this->calPrices($orderProduct, $produit);
            $orderProduct->setValider(true);
            $this->em->persist($orderProduct);
            $totalHT += $price['HT'];
            if (!isset($totalTVA)) {
                $totalTVA = (round($price['TTC'] - $price['HT'], 2));
            }else{
                $totalTVA += round($price['TTC'] - $price['HT'],2);
            }
        }
        $order->setTotaltva($totalTVA);
        $order->setTotalht(round($totalHT,2));
        $order->setTotalttc(round($totalHT + $totalTVA,2));
        $order->setValider(true);
        $this->em->persist($order);
        return $order;
    }

    /**
     * cr???? une commande pour un seul produit
     * @param $wbcli Wbcustomers
     * @param $cmd WbOrderProducts
     * @param $prod Products
     * @return Wborders
     */
    public function addprestaAffi(Wbcustomers $wbcli, WbOrderProducts $cmd, Products $prod): Wborders
    {
        $order=new Wborders();
        $cmd->setProduct($prod);
        //$cmd->setMultiple(1);
        $cmd->setOrder($order);
        $order->addProduct($cmd);
        $order->setNumcommande(($this->numerator->getCmdNumerate())->getNumCmd());
        $wbcli->addOrder($order);
        $this->calCmdWb($order);
        $this->em->persist($order);
        $this->em->persist($cmd);
        $this->em->persist($wbcli);
        $this->em->flush();
        return $order;
    }

    /**
     * @param $order Wborders
     * @param $wbcli Wbcustomers
     * @return mixed
     */
    public function addprestaFreeAffi(Wborders $order, Wbcustomers $wbcli): Wborders
    {
        $order->setNumcommande(($this->numerator->getCmdNumerate())->getNumCmd());
        $wbcli->addOrder($order);
        $this->calCmdWb($order);
        $this->em->persist($order);
        $this->em->persist($wbcli);
        $this->em->flush();
        return $order;
    }

    /**
     * @param $order Wborders
     * @param $wbcli Wbcustomers
     * @return mixed
     */
    public function editPrestaFree(Wborders $order, Wbcustomers $wbcli): Wborders
    {
        $this->calCmdWb($order);
        $this->em->persist($order);
        $this->em->persist($wbcli);
        $this->em->flush();
        return $order;
    }



    //todo revoir tout en dessous



    /**
     * @param Customers $customer
     * @param Numeratum $num
     * @param $tabmd
     * @return bool
     */
    public function newCmdClProduct(Customers $customer, Numeratum $num, $tabmd){

        $numclient=$customer->getNumclient();
        $user=$customer->getUser();
        $order=new Orders();

        foreach($tabmd as $md){
            $cmd=new OrderProducts();
            $prod=$this->productsRepository->find($md['idprod']);
            $cmd->setProduct($prod);
            $cmd->setMultiple($md['$qt']);
            $cmd->setOrder($order);
            $this->em->persist($cmd);
            $order->addProduct($cmd);
        }
        $order->setNumcommande($num->getNumCmd());
        $numclient->addOrder($order);

        if($this->calCmd($order)){
            $this->em->persist($order);
            $this->em->persist($numclient);
            $this->em->flush();
            return true;
        }else{
            return false;
        }

    }


    /**
     * @param $order Orders
     * @return Orders
     */
    public function calCmd22(Orders $order): Orders
    {
        $totalHT = 0;
        $totalTVA = 0;
        $cmds=$this->orderrepo->findByOrder($order);
        foreach($cmds as $cmd)
        {
            $produit = $cmd->getProduct();
            $price=$this->calPrices($cmd, $produit);
            $totalHT += $price['HT'];
            if (!isset($totalTVA)){
                $totalTVA = (round($price['TTC'] - $price['HT'], 2));
            }else{
                $totalTVA += round($price['TTC'] - $price['HT'], 2);
            }
        }
        $order->setTotaltva($totalTVA);
        $order->setTotalht(round($totalHT,2));
        $order->setTotalttc(round($totalHT + $totalTVA,2));
        return $order;
    }

    /**
     * @param $orderProducts OrderProducts
     * @param $order Orders
     * @return Orders
     */
    public function calOrderproduct(OrderProducts $orderProducts, Orders $order): Orders
    {
        $totalHT = 0;
        $totalTVA = 0;
        $produit = $orderProducts->getProduct();
        $price=$this->calPrices($orderProducts, $produit);
        $totalHT += $price['HT'];
        if (!isset($totalTVA)){
            $totalTVA = (round($price['TTC'] - $price['HT'], 2));
        }else{
            $totalTVA += round($price['TTC'] - $price['HT'], 2);
        }
        $order->setTotaltva($totalTVA);
        $order->setTotalht(round($totalHT,2));
        $order->setTotalttc(round($totalHT + $totalTVA,2));
        return $order;
       // $commande['token'] = bin2hex(random_bytes(20));

    }

    /**
     * @param CommandeFormule $formule
     * @param $order Orders
     * @return Orders
     */
    public function calfromuleproduct(CommandeFormule $formule, Orders $order): Orders
    {
        $totalHT = 0;
        $totalTVA = 0;
        $price=$this->calPrices($formule);
        $totalHT += $price['HT'];
        if (!isset($totalTVA)){
            $totalTVA = (round($price['TTC'] - $price['HT'], 2));
        }else{
            $totalTVA += round($price['TTC'] - $price['HT'], 2);
        }
        $order->setTotaltva($totalTVA);
        $order->setTotalht(round($totalHT,2));
        $order->setTotalttc(round($totalHT + $totalTVA,2));
        return $order;
        // $commande['token'] = bin2hex(random_bytes(20));

    }


    /**
     * @param $cmd
     * @param $produit
     * @return array
     */
    public function calPrices($cmd,$produit): array
    {
        if($cmd->isRemised()) {
            $tarif=$cmd->getPriceht();
            //$cmd->setPriceht($produit->getPrice());
        }else{
            $tarif=$produit->getPrice();
            $cmd->setPriceht($produit->getPrice());
        }
        if($produit->getUnit()=="%"){
            $price['HT'] = ($tarif * $cmd->getMultiple())/100;
            $price['TTC'] = (($tarif * $cmd->getMultiple())/100 ) * $produit->getTva()->getMultiplicate();

        }else{
            $price['HT'] = ($tarif * $cmd->getMultiple());
            $price['TTC'] = ($tarif * $cmd->getMultiple()) * ($produit->getTva()->getMultiplicate());
        }
         return $price;
    }
}