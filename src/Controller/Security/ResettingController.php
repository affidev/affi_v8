<?php



namespace App\Controller\Security;

use App\Email\RegistrationMailer;
use App\AffiEvents;
use App\Event\FormEvent;
use App\Repository\UserRepository;
use App\Event\FilterUserResponseEvent;
use App\Event\GetResponseNullableUserEvent;
use App\Event\GetResponseUserEvent;
use App\Util\TokenGeneratorInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("security/oderder/reseting/")
 */

class ResettingController extends AbstractController
{
    private EventDispatcherInterface $eventDispatcher;
    private $tokenGenerator;
    private $mailer;
    private $retryTtl;



    public function __construct(EventDispatcherInterface $eventDispatcher, TokenGeneratorInterface $tokenGenerator, RegistrationMailer $mailer)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
        $this->retryTtl = 3600;
    }



    /**
     * @Route("reset-what", name="resetting_reset")
     * @param Request $request
     * @param UserRepository $userRepository
     * @return RedirectResponse|Response|null
     * @throws NonUniqueResultException
     */

    public function reset(Request $request, UserRepository $userRepository)
    {
        $token = $request->query->get('token');
        $user = $userRepository->findUserByConfirmationToken($token);
        if (null === $user) {
            return new RedirectResponse($this->container->get('router')->generate('app_login'));
        }

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch($event,Affievents::RESETTING_RESET_INITIALIZE);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->createForm();
        $form->setData($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new FormEvent($form, $request);
            $this->eventDispatcher->dispatch($event,Affievents::RESETTING_RESET_SUCCESS);
            $this->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('profil_show');
                $response = new RedirectResponse($url);
            }
            $this->eventDispatcher->dispatch(new FilterUserResponseEvent($user, $request, $response), Affievents::RESETTING_RESET_COMPLETED);
            return $response;
        }

        return $this->render('security/Resetting/reset.html.twig', array(
            'token' => $token,
            'form' => $form->createView(),
        ));
    }

    protected function updateUser($user){
        $em=$this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return $user;
    }
}
