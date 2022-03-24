<?php

namespace App\Controller\Answers;

use App\Classe\affisession;
use App\Entity\Websites\Website;
use App\Lib\Links;
use App\Repository\Entity\WebsiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/notifi")
 */

class ReponsesController extends AbstractController
{

 use affisession;


    /**
     * @Route("/bienvenue/{slug}/{type}/{id}", options={"expose"=true} , defaults={"city"= null }, name="contact_keep")
     * @param Request $request
     * @param WebsiteRepository $websiteRepository
     * @param null $slug
     * @param null $id
     * @param null $type
     * @return Response
     */
    public function kepping(Request $request, WebsiteRepository $websiteRepository,$slug=null,$id=null,$type=null): Response
    {
        /** @var Website $website */
        $website=$websiteRepository->findWbBySlug($slug);
       $locate=$website->getLocality();
       $city=$locate->getSlugcity();

        $vartwig=$this->menuNav->templateControl(
            Links::CUSTOMER_LIST,
            'keep',
            "keep",
            'all');

        return $this->render('public/home.html.twig', [
            'locate'=>$locate,
            'replacejs'=>false,
            'city'=>$city,
            'vartwig'=>$vartwig,
            'website'=>$website,
            'directory'=>'page',
            'admin'=>[$this->admin,$this->permission]
        ]);
    }




}
