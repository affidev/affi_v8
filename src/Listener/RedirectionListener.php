<?php

namespace App\Listener;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;



class RedirectionListener
{
    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\TokenInterface|null
     */
    private $securityTokenStorage;
    /**
     * @var UrlGeneratorInterface
     */
    private $router;
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param TokenStorageInterface $container
     * @param SessionInterface $session
     * @param UrlGeneratorInterface $router
     */
    public function __construct(TokenStorageInterface $container, SessionInterface $session, UrlGeneratorInterface $router)
    {
        $this->session = $session;
        $this->router = $router;
        $this->securityTokenStorage = $container->getToken();
    }

    public function onKernelRequest(RequestEvent $event)
    {

        $route = $event->getRequest()->attributes->get('_route');
        if ($route == 'page_livraison' || $route == 'page_validation') {
            if ($this->session->has('panier')) {
                if (count($this->session->get('panier')) == 0)
                    $event->setResponse(new RedirectResponse($this->router->generate('page_panier')));
            }

            if (!is_object($this->securityTokenStorage->getUser())) {
                $this->session->getFlashBag()->add('notification','Vous devez vous identifier');
                $event->setResponse(new RedirectResponse($this->router->generate('app_login')));
            }
        }
    }
}