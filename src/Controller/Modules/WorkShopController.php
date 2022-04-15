<?php


namespace App\Controller\Modules;

use App\Classe\affisession;
use App\Entity\DispatchSpace\Spwsite;
use App\Entity\Marketplace\Offres;
use App\Form\DeleteType;
use App\Lib\MsgAjax;
use App\Module\Offrator;
use App\Repository\Entity\OffresRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/member/marketplace/shop/")
 */

class WorkShopController extends AbstractController
{
    use affisession;

    /**
     *
     * @Route("add-workshop-ajx", options={"expose"=true}, name="add_workshop_ajx")
     * @param Request $request
     * @param Offrator $offrator
     * @return JsonResponse
     * @throws \Exception
     */
    public function AddWorkShopAjx(Request $request, Offrator $offrator): JsonResponse
    {
        if($request->isXmlHttpRequest())
        {
            $req=$request->request->All();
            $taboffre=json_decode($req['taboffre']);
            if(!$this->getUserspwsiteOfWebsite($taboffre->idwb) || !$this->admin ) return new JsonResponse(MsgAjax::MSG_NOADMIN);
            $submittedToken = $request->request->get('token');
            if ($this->isCsrfTokenValid('edit-offre', $submittedToken)) {
                $issue = $offrator->newWorkshop($this->dispatch, $this->board,$req, $taboffre);
                return new JsonResponse($issue);
            }
        }
        return new JsonResponse(MsgAjax::MSG_ERRORRQ);
    }

    /**
     * @Route("/new-workshop/", name="new_generic_workshop")
     * @Route("new-workshop/{id}/", name="new_workshop")
     * @param $id
     * @param Offrator $offrator
     * @return Response
     * @throws NonUniqueResultException
     */
    public function newWorkShop(Offrator $offrator, $id=null): Response
    {
        if($id){
            if(!$this->getUserspwsiteOfWebsite($id) || !$this->admin )$this->redirectToRoute('cargo_public');
        }else{
            if(!$this->dispatch) throw new Exception('dispatch inconnu');
        }

        $taboffre=$offrator->preNewOffre();
        $vartwig=$this->menuNav->templatingadmin(
            'newworkshop',
            "workshop",
            $this->board,4);

        return $this->render('aff_websiteadmin/home.html.twig', [
            'directory'=>'shop',
            'replacejs'=>false,
            'website'=>$this->board,
            'board'=>$this->board,
            'taboffre'=>json_encode($taboffre),
            'offre'=>0,
            "content"=>"",
            'vartwig'=>$vartwig,
            'admin'=>[$this->admin,$this->permission]
        ]);
    }


    /**
     * @Route("editworkshop/{id}", name="edit_workshop")
     * @param OffresRepository $offresRepository
     * @param Offrator $offrator
     * @param $id
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function editWorkShop(OffresRepository $offresRepository,Offrator $offrator, $id): RedirectResponse|Response
    {
        /** @var Offres $offre */ //todo a revoir je pense trop de requete
        if(!$offre=$offresRepository->findPstQ2($id))return $this->redirectToRoute('api-error',['err'=>2]);
        $this->initBoardByKey($offre->getKeymodule());
        $taboffre=$offrator->preEditOffre($offre);
        /** @var Spwsite $pw */

        $vartwig=$this->menuNav->templatingadmin(
            'edit',
            "offre",
            $this->board,4);
        return $this->render('aff_websiteadmin/home.html.twig', [
            'directory'=>'shop',
            'replacejs'=>false,
            'website'=>$this->board,
            'board'=>$this->board,
            'product'=>$offre->getProduct(),
            'offre'=>$offre,
            'tabunique'=>json_encode(explode(';',$offre->getTabunique())),
            'taboffre'=>json_encode($taboffre),
            'vartwig'=>$vartwig,
            'admin'=>[$this->admin,$this->permission],
            'back'=> $this->generateUrl('module_shop',['city'=>$this->board->getLocality()->getCity(),'nameboard' => $this->board->getSlug()]),
        ]);
    }


    /**
     * @Route("form-delete-workshop/{id}", name="form-delete_workshop")
     * @param Request $request
     * @param OffresRepository $offresRepository
     * @param $id
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function formdeletet(Request $request, OffresRepository $offresRepository, $id): RedirectResponse|Response
    {
        /** @var Offres $offre */
        if(!$offre=$offresRepository->findPstQ2($id, $this->iddispatch))return $this->redirectToRoute('api-error',['err'=>2]);
        $this->initBoardByKey($offre->getKeymodule());
        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $offre->setDeleted(true);
            $this->em->persist($offre);
            $this->em->flush();
            $this->addFlash('info', 'offre supprimé.');
            return $this->redirectToRoute('show_shop', ['id' => $this->board->getId()]);
        }
        $vartwig = $this->menuNav->templatingadmin(
            'deleteoffre',
            'delte offre',
            $this->board,4);

        return $this->render('aff_websiteadmin/home.html.twig', [
            'directory'=>'shop',
            'replacejs'=>false,
            'form' => $form->createView(),
            'website' => $this->board,
            'board'=>$this->board,
            'vartwig' => $vartwig,
            'author' => $offre->getAuthor()->getId()==$this->iddispatch,
            'admin'=>[$this->admin,$this->permission]
        ]);
    }

    /**
     * @Route("/publied-workshop/{id}", name="publied_workshop")
     * @param OffresRepository $offresRepository
     * @param Offrator $offrator
     * @param $id
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function publiedWorkShop(OffresRepository $offresRepository, Offrator $offrator, $id): RedirectResponse|Response
    {
        if(!$offre=$offresRepository->findPstQ2($id, $this->iddispatch))return $this->redirectToRoute('api-error',['err'=>2]);
        /** @var Spwsite $pw */
        $this->initBoardByKey($offre->getKeymodule());
        if(!$pw) return $this->redirectToRoute('cargo_public');
        $offrator->publiedOneOffre($offre);
        return $this->redirectToRoute('boutique', ['id' => $this->board->getId()]);
    }

}