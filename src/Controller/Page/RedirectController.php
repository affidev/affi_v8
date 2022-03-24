<?php


namespace App\Controller\Page;


use App\Classe\affisession;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/redirect/page")
 * @IsGranted("ROLE_CUSTOMER")
 */


class RedirectController extends AbstractController
{
    use affisession;

    /**
     * @Route("redirection/{what}", name="redirect_action")
     * @return Response
     */
    public function becameCustomer($what=null) //todo complet
    {

        return $this->redirectToRoute('cargo_public');

        /*
        $template=$this->dispatch->templateControl(
            'PAGES',
            "",
            'redirectAction');

        switch ($what) {
            case "openlink":

                if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
                    $this->addFlash("redirect", "pour ouvrir un lien, vous devez d'abord activer votre compte");
                    return $this->redirectToRoute('activate.customer');

                } else {
                    $this->addFlash('redirect', 'Pour ouvrir un lien, vous devez être identifié');
                    return $this->render($this->useragent.'main_public/layout_base_vistore.html.twig', [
                        'template'=>$template,
                        'actions'=>[
                            ["path"=>"app_login","name"=>"Connectez-vous"],
                            ["path"=>"registration","name"=>"pas encore nscrit, ouvrez votre espace, c'est libre, gratuit et confidentiel"]
                        ]]);
                }
            break;

            default:
                $this->addFlash('redirect', "Nous n'avons pas trouver une issue à votre requete, merci de bien vouloir recommencer");
                return $this->render($this->useragent.'main_public/layout_base_vistore.html.twig', [
                    'template'=>$template,
                    'actions'=>[
                        ["path"=>"affichange","name"=>"retour"]
                    ]]);
        }
*/

    }
}