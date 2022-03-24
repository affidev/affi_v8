<?php


namespace App\Controller\Modules;

use App\Classe\affisession;
use App\Entity\Module\Formules;
use App\Form\DeleteType;
use App\Form\DuplicateType;
use App\Form\FormulesType;
use App\Module\Formulator;
use App\Repository\Entity\FormulesRepository;
use App\Service\Search\Searchcarte;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/member/wb/formules")
 * @IsGranted("ROLE_CUSTOMER")
 */

class FormuleController extends AbstractController
{
    use affisession;

    /**
     * @Route("/new-menu/", name="new_generic_menu")
     * @Route("/new-menu/{id}", options={"expose"=true}, name="new_menu")
     * @param Request $request
     * @param Searchcarte $searchcarte
     * @param Formulator $formulator
     * @param null $id
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function newMenu(Request $request,Searchcarte $searchcarte, Formulator $formulator,  $id=null): RedirectResponse|Response
    {
        if($id){
            if(!$this->getUserspwsiteOfWebsite($id) || !$this->admin )$this->redirectToRoute('cargo_public');
        }else{
            if(!$this->dispatch) throw new \Symfony\Component\Config\Definition\Exception\Exception('dispatch inconnu');
            $this->activeBoard();
        }
        $tabcarte=$searchcarte->findCarte($this->board->getCodesite());
        $formule = new Formules();
        $form=$this->createForm(FormulesType::class, $formule);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $articles=json_decode($request->request->get('formules')['listarticle']);
            $formulator->newFormule($form, $formule, $this->board, $articles, $this->board->getCodesite());
            $this->addFlash('infoprovider', 'nouvelle formule créée.');
            return $this->redirectToRoute('module_found', ['city'=>$this->board->getLocality()->getCity(),'nameboard' => $this->board->getSlug()]);
        }

        $vartwig=$this->menuNav->templatingadmin(
            'new',
            $this->board->getNamewebsite(),
            $this->board,
            5
        );

        return $this->render('aff_websiteadmin/home.html.twig', [
            'directory'=>'formule',
            'replacejs'=>false,
            'form' => $form->createView(),
            'tabcarte'=>$tabcarte,
            'board'=>$this->board,
            'website'=>$this->board,
            'vartwig'=>$vartwig,
        ]);
    }

    /**
     * @Route("/edit-menu-du-jour/{id}", options={"expose"=true}, name="edit_menu_day")
     * @param Request $request
     * @param Searchcarte $searchcarte
     * @param FormulesRepository $formulesRepository
     * @param Formulator $formulator
     * @param $id
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function editFormule(Request $request,Searchcarte $searchcarte, FormulesRepository $formulesRepository, Formulator $formulator, $id): RedirectResponse|Response
    {
        if(!$formule=$formulesRepository->findFormuleById($id)) throw new Exception('formule introuvable');
        if(!$this->getUserspwsiteOfWebsiteByKey($formule->getKeymodule()) || !$this->admin )$this->redirectToRoute('cargo_public');
        $tabcarte=$searchcarte->findCarte($this->board->getCodesite());

        $form=$this->createForm(FormulesType::class, $formule);
        $form->get('start')->setData($formule->getParution()->getStarttime());
        $form->get('end')->setData($formule->getParution()->getEndtime());
        $form->get('listarticle')->setData(json_encode($formulator->getlistarticles($formule)));
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $articles=json_decode($request->request->get('formules')['listarticle']);
            $formulator->editFormule($form, $formule,  $articles, $this->board);
            $this->addFlash('infoprovider', 'mise à jour effectuée.');
            return $this->redirectToRoute('module_found', ['city'=>$this->board->getLocality()->getCity(),'nameboard' => $this->board->getSlug()]);
        }
        $vartwig=$this->menuNav->templatingadmin(
            'edit',
            "edition formule",
            $this->board,5);

        return $this->render('aff_websiteadmin/home.html.twig', [
            'directory'=>'formule',
            'replacejs'=>false,
            'form' => $form->createView(),
            'website'=>$this->board,
            'board'=>$this->board,
            'tabcarte'=>$tabcarte,
            'formule'=>$formule,
            'vartwig'=>$vartwig,
            'admin'=>[$this->admin,$this->permission],
            'city'=>$this->board->getLocality()->getCity(),
            'back'=> $this->generateUrl('module_found',['city'=>$this->board->getLocality()->getCity(),'nameboard' => $this->board->getSlug()]),
        ]);
    }


    /**
     * @Route("/form-delete-formule/{id}", name="form-delete_formule")
     * @param Request $request
     * @param FormulesRepository $formulesRepository
     * @param Formulator $formulator
     * @param $id
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function deleteFormule(Request $request,FormulesRepository $formulesRepository,Formulator $formulator, $id): RedirectResponse|Response
    {
        if(!$formule=$formulesRepository->findFormuleById($id)) throw new Exception('formule introuvable');
        if(!$this->getUserspwsiteOfWebsiteByKey($formule->getKeymodule()) || !$this->admin )$this->redirectToRoute('cargo_public');

        $author=true;

        $form = $this->createForm(DeleteType::class, $formule);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $formulator->removeFormule($formule);
        return $this->redirectToRoute('module_found', ['city'=>$this->board->getLocality()->getCity(),'nameboard' => $this->board->getSlug()]);
        }
        $vartwig = $this->menuNav->templatingadmin(
        'delete',
        'delete formule',
            $this->board,5);


        return $this->render('aff_websiteadmin/home.html.twig', [
        'directory'=>'formule',
        'replacejs'=>false,
        'form' => $form->createView(),
        'website' => $this->board,
        'board'=>$this->board,
        'vartwig' => $vartwig,
        'author'=>$author,
        'admin'=>[$this->admin,$this->permission],
        'city'=>$this->board->getLocality()->getCity()
        ]);
    }

    /**
     * @Route("/form-duplicate-formule/{id}", name="form-duplicate_formule")
     * @param Request $request
     * @param FormulesRepository $formulesRepository
     * @param Formulator $formulator
     * @param $id
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function duplicateFormule(Request $request,FormulesRepository $formulesRepository, Formulator $formulator, $id): RedirectResponse|Response
    {

        if(!$formule=$formulesRepository->findFormuleById($id)) throw new Exception('formule introuvable');
        if(!$this->getUserspwsiteOfWebsiteByKey($formule->getKeymodule()) || !$this->admin )$this->redirectToRoute('cargo_public');


        $form = $this->createForm(DuplicateType::class, $formule);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formulator->duplicateFormule($this->board,$formule->getKeymodule(), $formule);
            return $this->redirectToRoute('module_found', ['city'=>$this->board->getLocality()->getCity(),'nameboard' => $this->board->getSlug()]);
        }
        $vartwig = $this->menuNav->templatingadmin(
                'duplicate',
                'duplicate formule',
            $this->board,5);

        return $this->render('aff_websiteadmin/home.html.twig', [
            'replacejs'=>false,
            'form' => $form->createView(),
            'website' => $this->board,
            'vartwig' => $vartwig,
            'directory'=>'formule',
            'admin'=>[$this->admin,$this->permission],
            'city'=>$this->board->getLocality()->getCity()
        ]);
    }

    /**
     * @Route("/form-publied-formule/{id}", name="form-publied_formule")
     * @param FormulesRepository $formulesRepository
     * @param Formulator $formulator
     * @param $id
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function publiedFormule(FormulesRepository $formulesRepository,Formulator $formulator, $id): RedirectResponse|Response
    {
        if(!$formule=$formulesRepository->findFormuleById($id)) throw new Exception('formule introuvable');
        if(!$this->getUserspwsiteOfWebsiteByKey($formule->getKeymodule()) || !$this->admin )$this->redirectToRoute('cargo_public');

        $formulator->publiedFormule($formule);
        return $this->redirectToRoute('module_found', ['city'=>$this->board->getLocality()->getCity(),'nameboard' => $this->board->getSlug()]);
    }

}