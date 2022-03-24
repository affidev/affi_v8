<?php


namespace App\Controller\Security;


use App\Entity\User;
use App\AffiEvents;
use App\Exeption\RedirectException;
use App\Repository\Entity\TabDotWbRepository;
use App\Repository\UserRepository;
use App\Event\GetResponseUserEvent;
use App\Service\Registration\Sessioninit;
use App\Util\CanonicalFieldsUpdater;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("security/oderder/check")
 */

class CheckRegistrationController extends AbstractController
{
    private EventDispatcherInterface $eventDispatcher;
    private TokenStorageInterface $tokenStorage;
    private CanonicalFieldsUpdater $canonicalFieldsUpdater;
    private Sessioninit $sessioninit;

    public function __construct(EventDispatcherInterface $eventDispatcher, TokenStorageInterface $tokenStorage, CanonicalFieldsUpdater $canonicalFieldsUpdater,Sessioninit $sessioninit)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->tokenStorage = $tokenStorage;
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
        $this->sessioninit = $sessioninit;
    }


    /**
     * @Route("/check-mail", name="registration_check_email")
     * @param RequestStack $requestStack
     * @param UserRepository $userRepository
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function checkEmail(RequestStack $requestStack, UserRepository $userRepository): RedirectResponse|Response
    {
        $email = $requestStack->getSession()->get('send_confirmation_email/email');
        if (empty($email)) {
            return new RedirectResponse($this->generateUrl('new_identify'));
        }
        $requestStack->getSession()->remove('send_confirmation_email/email');
        $emailcanonical= $this->canonicalFieldsUpdater->canonicalizeEmail($email);
        $user = $userRepository->findUserByEmail($emailcanonical);
        if (null === $user) {
            return $this->redirectToRoute('app_login');
        }
        $vartwig=['maintwig'=>"check_email",'title'=>"Votre espace AffiChanGe est confirmé"];
        return $this->render('aff_security/home.html.twig', [
            'directory'=>'registration',
            'replacejs'=>$replacejs??null,
            'vartwig'=>$vartwig,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/no-check-mail/{user}", name="no-registration_check_email")
     * @param $user User
     * @return RedirectResponse|Response
     */
    public function nocheckEmail($user)
    {
        if (null === $user) {
            return $this->redirectToRoute('app_login');
        }

        $vartwig=['maintwig'=>"no_check_email",'title'=>"no check mail"];
        return $this->render('aff_security/home.html.twig', [
            'directory'=>'registration',
            'replacejs'=>$replacejs??null,
            'vartwig'=>$vartwig,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/Confirme-inscription/{token}", name="registration_confirm")
     * @param Request $request
     * @param $token
     * @param UserRepository $reposiUser
     * @return RedirectResponse
     * @throws NonUniqueResultException
     * @throws RedirectException
     */
    public function confirmByTokenMail(Request $request, $token, UserRepository $reposiUser)
    {

        $user = $reposiUser->findUserByConfirmationToken($token);
        if (null === $user) {
            return $this->redirectToRoute('app_login');
        }
        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch($event, AffiEvents::REGISTRATION_CONFIRM );
        $em=$this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        if (null === $response = $event->getResponse()) {
            $this->sessioninit->initCustomer($user);
            $url = $this->generateUrl('confirmed');
            $response = new RedirectResponse($url);
        }

        //en stand by pour l'instant
        // $this->eventDispatcher->dispatch(Affievents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }

    /**
     * @Route("/Confirme-invitation/{token}", name="dispatch_confirm")
     * @param Request $request
     * @param $token
     * @param UserRepository $reposiUser
     * @return RedirectResponse
     * @throws NonUniqueResultException
     * @throws RedirectException
     */
    public function confirmInviteDispatchTokenMail(Request $request, $token, UserRepository $reposiUser): RedirectResponse
    {
        $user = $reposiUser->findUserByConfirmationToken($token);
        if (null === $user) {
            return $this->redirectToRoute('app_login');  // todo faire une redirction pour informer de l'echec
       }
        $user->setConfirmationToken(null);
        $user->setEnabled(true);
        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch($event, AffiEvents::REGISTRATION_CONFIRM );
        $em=$this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        if (null === $response = $event->getResponse()) {
            $this->sessioninit->initCustomer($user);
            $url = $this->generateUrl('confirmed');
            $response = new RedirectResponse($url);
        }

        //en stand by pour l'instant
        // $this->eventDispatcher->dispatch(Affievents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }

    /**
     * Tell the user his account is now confirmed.
     * @Route("/registration-confirmee", name="confirmed")
     * @param TabDotWbRepository $tabDotWbRepository
     * @return Response
     */
    public function confirmedInscription(TabDotWbRepository $tabDotWbRepository): Response
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof User) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $tabdot = $tabDotWbRepository->findOneBy(['email' => $user->getEmailCanonical()]);
        if ($tabdot) return $this->redirectToRoute('confirm_invit_admin_website',['id'=>$tabdot->getWebsite()->getId()]);

        $vartwig = ['maintwig' => "confirmed", 'title' => "Bravo, votre espace AffiChanGe est ouvert"];
        return $this->render('aff_security/home.html.twig', [
            'directory' => 'registration',
            'replacejs' => $replacejs ?? null,
            'vartwig' => $vartwig,
            'user' => $user,
            'tag' => ['name' => $city ?? null, 'active' => true, 'l_class' => "init"],
        ]);
    }

    /**
     * @param SessionInterface $session
     * @return string|null
     */
    private function getTargetUrlFromSession(SessionInterface $session): ?string
    {
       // dump($this->tokenStorage->getToken(),$session);
        $key = sprintf('_security.%s.target_path', $this->tokenStorage->getToken()->getProviderKey());

        if ($session->has($key)) {
            return $session->get($key);
        }
        return null;
    }
}