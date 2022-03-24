<?php


namespace App\Controller\Prospecting;


use App\Classe\affisession;
use App\Form\TestinmailType;
use App\Repository\Entity\ContactRepository;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/web/admin/")
 * @IsGranted("ROLE_CUSTOMER")
 */

class ProspectController extends AbstractController
{
    use affisession;

    /**
     * @Route("contact/{id}/{slug}", name="show_contact")
     * @param $id
     * @param $slug
     * @param ContactRepository $contactRepository
     * @return Response
     */
    public function showContact($id, $slug, ContactRepository $contactRepository)
    {
        if(!$this->getUserspwsiteOfWebsiteSlug($slug))return $this->redirectToRoute('api-error',['err'=>1]);

        $contact=$contactRepository->find($id);

        $vartwig=$this->menuNav->templatingspaceWeb(
            null,
            "prospect/contact",
            $this->website,
            null);
        return $this->render('website/home.html.twig', [
            'agent'=>$this->useragent,
            'vartwig'=>$vartwig,
            'website'=>$this->website,
            'iddispath'=>$this->iddispatch,
            'directory'=>'member',
            'admin'=>[$this->admin,$this->permission]
        ]);
    }

    /**
     * @Route("testting", name="testing")
     * @param Request $request
     * @param \App\Controller\Prospecting\Notificationor $mailer
     * @return Response
     * @throws NonUniqueResultException
     */
    public function testtingMail(Request $request, Notificationor $mailer)
    {
        $dispatch=$this->repodispacth->findLinkContact($this->iddispatch);
        $link=""; //$dispatch->getSpwsite()[0]->getWebsite()->getModules()[0]->getLinkone();
        $form=$this->createForm(TestinmailType::class);
        $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $email =  $form['email']->getData();
                $rep=$mailer->sendfirstWord($email, $link);
                if($rep){
                    $this->addFlash('meil','Ok, good wind !!!');
                }else{
                    $this->addFlash('meil','bad, no wind !!!');
                }
                return $this->redirectToRoute('testing_confirm');
            }
        $vartwig=$this->dispatch->templatingspaceWeb(
            null,
            "prospect/testing",
            'Spaceweb',
            null);
        return $this->render('layout/layout.html.twig', [
            'agent'=>$this->useragent,
            'vartwig'=>$vartwig,
            'spaceweb'=>$this->getDispatch(),
            'form'=>$form->createView(),
            'admin'=>$this->admin
            ]);
    }


    /**
     * @Route("confirm-first", name="testing_confirm")
     */
    public function testingConfirm()
    {
        $vartwig=$this->dispatch->templatingspaceWeb(
            null,
            "notifs/confirm-flash",
            'Spaceweb',
        null);
        return $this->render('layout/layout.html.twig', [
            'agent'=>$this->useragent,
            'vartwig'=>$vartwig,
            'spaceweb'=>$this->getDispatch(),
            'admin'=>$this->admin
        ]);
    }
}