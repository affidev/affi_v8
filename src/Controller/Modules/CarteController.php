<?php


namespace App\Controller\Modules;

use App\Classe\affisession;
use App\Entity\DispatchSpace\Spwsite;
use App\Entity\Module\Formules;
use App\Entity\Food\ArticlesFormule;
use App\Form\CarteType;
use App\Form\DeleteType;
use App\Form\DuplicateType;
use App\Module\Artfoodator;
use App\Module\Formulator;
use App\Repository\Entity\ArticlesFormuleRepository;
use App\Repository\Entity\FormulesRepository;
use App\Repository\Entity\ModuleListRepository;
use App\Service\Search\Listpublications;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/member/marketplace/shop/")
 */

class CarteController extends AbstractController
{
    use affisession;

    /**
     * @Route("new-carte/{id}", name="new_carte")
     * @param Request $request
     * @param $id
     * @param Artfoodator $artfoodator
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function newCarte(Request $request, $id, Artfoodator $artfoodator): RedirectResponse|Response
    {
        if(!$this->getUserspwsiteOfWebsite($id) || !$this->admin )$this->redirectToRoute('cargo_public');

        foreach ($this->board->getListmodules() as $module){
            $tabActivities[]=$module->getClassmodule();
        }

        $carte = new ArticlesFormule();
        $form=$this->createForm(CarteType::class, $carte);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $artfoodator->newCarte($this->board->getCodesite() , $form, $carte);
            $this->addFlash('infoprovider', 'nouvel article créé.');
            return $this->redirectToRoute('module_carte', ['city'=>$this->board->getLocality()->getCity(),'nameboard' => $this->board->getSlug()]);
        }

        $vartwig=$this->menuNav->templatingadmin(
            'newcarte',
            $this->board->getNamewebsite(),
            $this->board,
            5
        );

        return $this->render('aff_websiteadmin/home.html.twig', [
            'directory'=>'food',
            'replacejs'=>false,
            'form' => $form->createView(),
            'board'=>$this->board,
            'website'=>$this->board,
            'vartwig'=>$vartwig,
        ]);
    }

    /**
     * @Route("/edit-carte/{id}", options={"expose"=true}, name="edit_carte")
     * @param Request $request
     * @param ArticlesFormuleRepository $carterepo
     * @param Artfoodator $artfoodator
     * @param $id
     * @return RedirectResponse|Response
     */
    public function editCarte(Request $request,  ArticlesFormuleRepository $carterepo, Artfoodator $artfoodator, $id): RedirectResponse|Response
    {

        if(!$carte = $carterepo->find($id)) throw new Exception('carte introuvable');
        if(!$this->getUserspwsiteOfWebsiteByKey($carte->getKeymodule()) || !$this->admin )$this->redirectToRoute('cargo_public');

        $form=$this->createForm(CarteType::class, $carte);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $artfoodator->editCarte($this->board->getCodesite() , $form, $carte);
            return $this->redirectToRoute('module_carte',['city'=>$this->board->getLocality()->getCity(),'nameboard' => $this->board->getSlug()]);
        }

        $vartwig=$this->menuNav->templatingadmin(
            'edit',
            "edition carte",
            $this->board,5);

        return $this->render('aff_websiteadmin/home.html.twig', [
            'directory'=>'food',
            'replacejs'=>false,
            'form' => $form->createView(),
            'board'=>$this->board,
            'website'=>$this->board,
            'carte'=>$carte,
            'back'=> $this->generateUrl('module_carte',['city'=>$this->board->getLocality()->getCity(),'nameboard' => $this->board->getSlug()]),
            'vartwig'=>$vartwig,
        ]);
    }


    /**
     * @Route("/form-delete-carte/{id}", name="delete_carte")
     * @param Request $request
     * @param ArticlesFormuleRepository $carterepo
     * @param Artfoodator $artfoodator
     * @param $id
     * @return RedirectResponse|Response
     */
    public function deleteCarte(Request $request,ArticlesFormuleRepository $carterepo,Artfoodator $artfoodator, $id): RedirectResponse|Response
    {
        if(!$carte = $carterepo->find($id)) throw new Exception('carte introuvable');
        if(!$this->getUserspwsiteOfWebsiteByKey($carte->getKeymodule()) || !$this->admin )$this->redirectToRoute('cargo_public');

        $form = $this->createForm(DeleteType::class, $carte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $artfoodator->removeCarte($carte);
            return $this->redirectToRoute('module_carte', ['city'=>$this->board->getLocality()->getCity(),'nameboard' => $this->board->getSlug()]);
        }
        $vartwig = $this->menuNav->templatingadmin(
            'delete',
            'delete carte',
            $this->board,5);


        return $this->render('aff_websiteadmin/home.html.twig', [
            'replacejs'=>false,
            'form' => $form->createView(),
            'website'=>$this->board,
            'board'=>$this->board,
            'carte'=>$carte,
            'vartwig'=>$vartwig,
            'directory'=>'food',
            'admin'=>[$this->admin,$this->permission]
        ]);
    }



    //******************************************* TODO ***************************************************//
    /**
     * @Route("/form-duplicate-Carte/{id}", name="form-duplicate_carte")
     * @param Request $request
     * @param FormulesRepository $formulesRepository
     * @param Formulator $formulator
     * @param $id
     * @return RedirectResponse|Response
     */
    public function duplicateCarte(Request $request,FormulesRepository $formulesRepository,Formulator $formulator, $id): RedirectResponse|Response
    {
        /** @var Formules $formule */
        $formule = $formulesRepository->findFormule($id);
        if(!$formule)return $this->redirectToRoute('api-error',['err'=>2]);

        /** @var Spwsite $pw */
        $pw=$this->getUserspwsiteOfWebsite($website->getId());
        if(!$pw) return $this->redirectToRoute('cargo_public');
        $form = $this->createForm(DuplicateType::class, $formule);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formulator->duplicateFormule($this->board, $formule);
            return $this->redirectToRoute('list_formule', ['id' => $website->getId()]);
        }
        $vartwig = $this->menuNav->templatingspaceWeb(
            null,
            'formule/duplicate',
            'duplicate formule',
            $website->getNamewebsite());


        return $this->render('website/home.html.twig', [
            'form' => $form->createView(),
            'website' => $website,
            'vartwig' => $vartwig,
            'directory'=>'member',
            'pw'=>$pw,
            'admin'=>[$pw->isAdmin(),$this->permission]
        ]);
    }

    /**
     * @Route("/form-publied-carte/{id}", name="form-publied_carte")
     * @param Request $request
     * @param FormulesRepository $formulesRepository
     * @param Formulator $formulator
     * @param $id
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function publiedFormule(Request $request,FormulesRepository $formulesRepository,Formulator $formulator, $id)
    {
        /** @var Formules $formule */
        $formule = $formulesRepository->findFormule($id);
        if(!$formule)return $this->redirectToRoute('api-error',['err'=>2]);
        $website=$formule->getWebsite();
        /** @var Spwsite $pw */
        $pw=$this->getUserspwsiteOfWebsite($website->getId());
        if(!$pw) return $this->redirectToRoute('cargo_public');
        $formulator->publiedFormule($formule);
        return $this->redirectToRoute('list_formule', ['id' => $website->getId()]);
    }

}