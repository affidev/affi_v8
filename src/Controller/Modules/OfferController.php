<?php


namespace App\Controller\Modules;

use App\Classe\affisession;
use App\Entity\DispatchSpace\Spwsite;
use App\Entity\Module\Recrutation;
use App\Entity\Websites\Website;
use App\Form\DeleteType;
use App\Repository\Entity\ContactationRepository;
use App\Repository\Entity\ModuleTypeRepository;
use App\Repository\Entity\PostRepository;
use App\Repository\Entity\SpwsiteRepository;
use App\Repository\Entity\WebsiteRepository;
use App\Service\Calendar\Calendator;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/module/offer")
 * @IsGranted("ROLE_CUSTOMER")
 */

class OfferController extends AbstractController
{
    use affisession;

    /**
     * @Route("/newoffer/{id}/", name="new_offer") //todo
     * @param Request $request
     * @param ModuleTypeRepository $moduleTypeRepository
     * @param WebsiteRepository $websiteRepository
     * @param Calendator $calendator
     * @param $id
     * @param null $month
     * @param null $year
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function newOffert(Request $request, ModuleTypeRepository $moduleTypeRepository, WebsiteRepository $websiteRepository, Calendator $calendator, $id, $month=null, $year=null)
    {
        /** @var Spwsite $pw */
        $pw=$this->getUserspwsiteOfWebsite($id);
        if(!$pw || !$this->admin) return $this->redirectToRoute('cargo_public');
        $website=$pw->getWebsite();

        $vartwig=$this->dispatch->templatingspaceWeb(
            $website,
            'website/post/formaddarticleadmin',
            "post",
            $website->getNamewebsite());
        return $this->render('website/'.$this->useragent.'/offert/home.html.twig', [
            'agent'=>$this->useragent,
            'website'=>$website,
            'post'=>0,
            'pw'=>$pw,
            'pwsite'=>['isadmin'=>false],
            'vartwig'=>$vartwig,
            'admin'=>[$this->admin,$this->permission]
        ]);
    }

    /**
     * @Route("/editoffer/{id}", name="edit_offer") //todo
     * @param Request $request
     * @param Calendator $calendator
     * @param null $month
     * @param null $year
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function editOffer(Request $request, PostRepository $postationRepository, $id)
    {
        // $post=$postationRepository->findPstQ2($id, $this->iddispatch); // avec controme de l'auteur
        $post=$postationRepository->findPstQ0($id); // sans controle de l'auteur pour acces superadmin
        if(!$post) return $this->redirectToRoute('api-error',['err'=>2]);
        if($post->getHtmlcontent()->getFileblob()){
            $content=file_get_contents($post->getHtmlcontent()->getWebPathblob());
        }else{
            $content="";
        }
        $website=$post->getWebsite();
        /** @var Spwsite $pw */
        $pw=$this->getUserspwsiteOfWebsite($post->getWebsite()->getId());

        $vartwig=$this->dispatch->templatingspaceWeb(
            $website,
            'website/post/edit',
            "post",
            "post");

        return $this->render('layout/layout_post.html.twig', [
            'agent'=>$this->useragent,
            'website'=>$website,
            'posta'=>$post,
            'post'=>$post->getId(),
            'content'=>$content,
            'pw'=>$pw,
            'vartwig'=>$vartwig,
            'admin'=>[$pw->isAdmin(),$this->permission]
        ]);
    }


    /**
     * @Route("/form-delete-offer/{id}", name="form-delete_offer") //todo
     * @param Request $request
     * @param PostRepository $postationRepository
     * @param $id
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function formdeletetOffer(Request $request, PostRepository $postationRepository, $id)
    {
        /** @var Postation $post */
        $post=$postationRepository->findPstQ2($id, $this->iddispatch);
        $pw=$this->getUserspwsiteOfWebsite($post->getWebsite()->getId());
        if($post) {
            $website=$post->getWebsite();
            $form = $this->createForm(DeleteType::class, $post);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $post->setDeleted(true);
                $this->em->persist($post);
                $this->em->flush();
                $this->addFlash('info', 'post supprimé.');
                return $this->redirectToRoute('tab_spaceweb', ['slug' => $website->getSlug()]);
            }
            $vartwig = $this->dispatch->templatingspaceWeb(
                'forms/deletepost',
                'delte post',
                $website->getNamewebsite());

            return $this->render('layout/layout_wb.html.twig', [
                'agent' => $this->useragent,
                'form' => $form->createView(),
                'website' => $website,
                'vartwig' => $vartwig,
                'pw'=>$pw,
                'author' => $post->getAuthor()->getId()==$this->iddispatch ?true:false,
                'admin'=>[$pw->isAdmin(),$this->permission]
            ]);
        }
        return $this->redirectToRoute('api-error',['err'=>2]);
    }

    /**
     * @Route("/process-module-offer/{id}", options={"expose"=true}, name="process_offer")
     * @param SpwsiteRepository $spwsiteRepository
     * @param ContactationRepository $contactationRepository
     * @param $slug
     * @param null $id
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function processOffer($id)
    {
        $pw=$this->getUserspwsiteOfWebsite($id);
        if(!$this->admin or count($this->permission)<3)return $this->redirectToRoute('cargo_public');
        /** @var Website $website */
        $website=$pw->getWebsite();
        $offertation=$website->getModule()->getOffertation()??false;
        $test1=($email=$website->getTemplate()->getEmailspaceweb())?true:false;
        $test2=($sector=$website->getTemplate()->getSector())?true:false;
        $vartwig=$this->dispatch->templatingspaceWeb(
            'main_spaceweb/modules/process-offer',
            'module de contact',
            $website->getNamewebsite());
        return $this->render('website/'.$this->useragent.'/offert/home..html.twig', [
            'agent'=>$this->useragent,
            'vartwig' => $vartwig,
            'spaceweb'=>$website,
            'website'=>$website,
            'offertation'=>$offertation,
            'cargos'=>[],
            'test1'=>$test1,
            'test2'=>$test2,
            'email'=>$email,
            'sector'=>$sector,
            'admin'=>[$this->admin,$this->permission]
        ]);
    }


    /**
     * @Route("/creation-module-offer/{id}", name="active_offer") //todo
     * @param Offatar $offatar
     * @param $id
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws NonUniqueResultException
     */
    public function newModOffer(Offatar $offatar, $id)
    {
        $pw=$this->getUserspwsiteOfWebsite($id);
        if(!$this->admin or count($this->permission)<3)return $this->redirectToRoute('cargo_public');
        /** @var Website $website */
        $website=$pw->getWebsite();
        $offertation = new Recrutation();
        $offatar->newoffator(['website'=>$website, 'offertation'=>$offertation]);
        $this->addFlash('infoprovider', 'nouveau module de recrutement ok.');
        return $this->redirectToRoute('spaceweb_mod',['id'=>$website->getId()]);
    }


    /**
     * @Route("/desactive-module-offer/{id}", name="desactiv_offer") //todo
     * @param Offatar $offatar
     * @param $id
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws NonUniqueResultException
     */
    public function desactivoffer(Offatar $offatar, $id)
    {
        $pw=$this->getUserspwsiteOfWebsite($id);
        if(!$this->admin or count($this->permission)<3)return $this->redirectToRoute('cargo_public');
        /** @var Website $website */
        $website=$pw->getWebsite();
        $offertation = new Recrutation();
        $offatar->desactivOffertation(['website'=>$website, 'offertation'=>$offertation]);
        $this->addFlash('infoprovider', 'module de recrutement désactivé.');
        return $this->redirectToRoute('spaceweb_mod',['id'=>$website->getId()]);
    }

    /**
     * @Route("/reinit-module-offer/{id}", name="reinit_offer") //todo
     * @param Offatar $offatar
     * @param $id
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws NonUniqueResultException
     */
    public function reinitoffer(Offatar $offatar, $id)
    {
        $pw=$this->getUserspwsiteOfWebsite($id);
        if(!$this->admin or count($this->permission)<3)return $this->redirectToRoute('cargo_public');
        /** @var Website $website */
        $website=$pw->getWebsite();
        $offertation = new Recrutation();
        $offatar->reinitOffertation(['website'=>$website, 'offertation'=>$offertation]);
        $this->addFlash('infoprovider', 'module de recrutement désactivé.');
        return $this->redirectToRoute('spaceweb_mod',['id'=>$website->getId()]);
    }

}