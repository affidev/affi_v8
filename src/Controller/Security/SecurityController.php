<?php

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


/**
 * @Route("/security/oderder/identif/")
 */

class SecurityController extends AbstractController
{

    /**
     * @Route("login", options={"expose"=true}, name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @param SessionInterface $session
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils, SessionInterface $session): Response
    {
        // if ($this->getUser()) {
        //   $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        if (preg_match('/mob/i', $_SERVER['HTTP_USER_AGENT'])) {
            $session->set('agent','mobile/');
        } else {
            $session->set('agent','desk/');
        }
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        $vartwig=['maintwig'=>"login",'title'=>"connexion"];
        return $this->render('aff_security/home.html.twig', [
            'directory'=>'log',
            'vartwig'=>$vartwig,
            'replacejs'=>null,
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("logout", name="app_logout")
     * @param SessionInterface $session
     * @throws \Exception
     */
    public function logout(SessionInterface $session)
    {
        $session->invalidate();
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

}
