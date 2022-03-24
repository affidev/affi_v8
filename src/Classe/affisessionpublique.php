<?php


namespace App\Classe;


use App\Service\MenuNavigator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

trait affisessionpublique
{
    private $useragent;
    private EntityManagerInterface $em;
    private MenuNavigator $menuNav;
    private RequestStack $requestStack;


    public function __construct(EntityManagerInterface $em, MenuNavigator $menuNav, RequestStack $requestStack){
        $this->pregmatch();
        $this->menuNav=$menuNav;
        $this->em = $em;
        $this->requestStack=$requestStack;
    }
    protected function pregmatch(){
        if (preg_match('/mob/i', $_SERVER['HTTP_USER_AGENT'])) {
            $this->useragent = "mobile/";
        } else {
            $this->useragent = "desk/";
        }
    }
}