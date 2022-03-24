<?php


namespace App\Security;


use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

class LoginManager implements LoginManagerInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var UserCheckerInterface
     */
    private $userChecker;

    /**
     * @var SessionAuthenticationStrategyInterface
     */
    private $sessionStrategy;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var RememberMeServicesInterface
     */
    private $rememberMeService;


    private $session;

    /**
     * LoginManager constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     * @param UserCheckerInterface $userChecker
     * @param SessionAuthenticationStrategyInterface $sessionStrategy
     * @param RequestStack $requestStack
     * @param SessionInterface $session
     * @param RememberMeServicesInterface|null $rememberMeService
     */
    public function __construct(TokenStorageInterface $tokenStorage, UserCheckerInterface $userChecker,
                                SessionAuthenticationStrategyInterface $sessionStrategy,
                                RequestStack $requestStack,
                                SessionInterface $session,
                                RememberMeServicesInterface $rememberMeService = null
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->userChecker = $userChecker;
        $this->sessionStrategy = $sessionStrategy;
        $this->requestStack = $requestStack;
        $this->rememberMeService = $rememberMeService;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    final public function logInUser($firewallName, User $user, Response $response = null)
    {
        $this->userChecker->checkPreAuth($user);

        $token = $this->createToken($firewallName, $user);
        $request = $this->requestStack->getCurrentRequest();

        if (null !== $request) {
            $this->sessionStrategy->onAuthentication($request, $token);

            if (null !== $response && null !== $this->rememberMeService) {
                $this->rememberMeService->loginSuccess($request, $response, $token);
            }
        }
        $this->tokenStorage->setToken($token);
    }

    /**
     * @param string        $firewall
     * @param User $user
     *
     * @return UsernamePasswordToken
     */
    protected function createToken($firewall, User $user)
    {
        return new UsernamePasswordToken($user, null, $firewall, $user->getRoles());
    }
}