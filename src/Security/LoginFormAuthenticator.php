<?php

namespace App\Security;


use App\Exeption\RedirectException;
use App\Repository\UserRepository;
use App\Service\Registration\Sessioninit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;


class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;
    private CsrfTokenManagerInterface $csrfTokenManager;
    private UserPasswordHasherInterface $passwordEncoder;
    private ?UserInterface $user = null;
    private EventDispatcherInterface $eventDispatcher;
    private UserRepository $userRepository;
    private UrlMatcherInterface $urlMatcher;
    private Sessioninit $sessionInit;
    private EntityManagerInterface $entityManager;


    /**
     *
     * @param UserRepository $userRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param UserPasswordHasherInterface $passwordEncoder
     * @param Sessioninit $sessionInit
     */



    public function __construct(UserRepository $userRepository, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordHasherInterface $passwordEncoder, Sessioninit $sessionInit)
    {
        $this->userRepository = $userRepository;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->sessionInit = $sessionInit;

    }

    public function supports(Request $request):bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * @throws NonUniqueResultException
     */
    public function authenticate(Request $request): PassportInterface
    {
        $user = $this->userRepository->findUserByEmail($request->get('email'));
        if (!$user) {
            throw new UserNotFoundException();
        }
        $email = $request->get('email', '');
        $password = $request->get('password');
        $csrfToken = $request->get('_csrf_token');
        $request->getSession()->set(Security::LAST_USERNAME, $email);


        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
                new PasswordUpgradeBadge(
                    $request->get('password'),
                    $this->userRepository
                )
            ]
        );
    }


    /**
     * @throws NonUniqueResultException
     * @throws RedirectException
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): RedirectResponse
    {
          $user=$token->getUser();
          if(!$user->getEnabled()){
              $loguser=$user;
              $token->setAuthenticated(false);
              return new RedirectResponse($this->urlGenerator->generate('no-registration_check_email', array('user' => $loguser)));
          }
         // if(!$user->getCharte()) return new RedirectResponse($this->urlGenerator->generate('spaceweb_charte')); todo normalement n'a plus lieu d'??tre

          $defaultWb=$this->sessionInit->initCustomer($user);

            if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
                return new RedirectResponse($targetPath);
            }else{
                $locality=$defaultWb->getLocality()->getSlugcity()??'bouaye';
                return new RedirectResponse($this->urlGenerator->generate('cargo_public',['slugcity'=>$locality]));
            }

        //throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        return new RedirectResponse($this->urlGenerator->generate('app_login',['error'=>$exception->getMessage()]));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
