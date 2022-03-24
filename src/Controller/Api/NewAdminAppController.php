<?php


namespace App\Controller\Api;

use App\Classe\affisession;
use App\Entity\Websites\Website;
use App\Repository\Entity\SpwsiteRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/Aouth/admin/")
 * @IsGranted("ROLE_CUSTOMER")
 */
class NewAdminAppController extends AbstractController
{

use affisession;


    /**
     * @Route("new-admin/{slug}", name="new_adminapp")
     * @param SpwsiteRepository $spwsiteRepository
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function newAdminApp(SpwsiteRepository $spwsiteRepository, $slug){
        $spwsite=$spwsiteRepository->getOneSpwWithWebsiteThanDispathBySlug($slug, $this->iddispatch);
        if(!$spwsite)return $this->redirectToRoute('cargo_public',['locate'=>$this->session->get('city')]);
        /** @var Website $website */
        $website=$spwsite->getWebsite();
        $client = HttpClient::create();

        $dispatch=$this->getDispatch();
        $response = $client->request('GET', $website->getUrl().'identify-app/'.$website->getCodesite());
       // return New Response('<!DOCTYPE html><html lang="fr"><body>'.$response->getContent().'</body></html>');

             if($response->getStatusCode()==200){
                 $key=json_decode($response->getContent(), true)['key'];
                 $response2 = $client->request('POST', $website->getUrl().'newadmin-app',[
                     'body'=>[
                         'admin' => $this->getUser()->getEmailCanonical(),
                         'password'=>'Mdj0713!',
                         'idapp'=>$this->getParameter('app_affi'),
                         'key'=>$key]
                 ]);
                if($response2->getStatusCode()==200){
                   // return New Response('<!DOCTYPE html><html lang="fr"><body>'.$response2->getContent().'</body></html>');
                    $token=json_decode($response2->getContent(), true)['token'];
                    $spwsite->setToken($token);
                    $this->em->persist($spwsite);
                    $this->em->flush();
                 }
                 return $this->redirectToRoute('acces_admin',['id'=>$website->getId()]);
            }
        return $this->redirectToRoute('spaceweb_mod',[
            'slug'=>$dispatch->getSlug()]); //todo gerer l'erreur
    } 
}