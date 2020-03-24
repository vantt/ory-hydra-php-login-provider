<?php

namespace App\Security;

use App\Entity\Drupal7User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator {
    use TargetPathTrait;

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $encoderFactory;

    private $route;

    public function __construct(EntityManagerInterface $entityManager, EncoderFactoryInterface $encoderFactory, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager) {
        $this->entityManager    = $entityManager;
        $this->encoderFactory   = $encoderFactory;
        $this->urlGenerator     = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;

    }

    public function supports(Request $request) {
        $route       = $request->attributes->get('_route');
        $isPost      = $request->isMethod('POST');
        $this->route = $route;

        return $isPost && ('app_login' === $route || 'app_consent' === $route);
    }

    public function getCredentials(Request $request) {
        $data = $request->request->get('form', []);

        if ('app_login' === $this->route) {
            $credentials = [
              'username'   => $data['username'] ?? null,
              'password'   => $data['password'] ?? null,
              'csrf_token' => $data['_csrf_token'] ?? null,
            ];

            $token = new CsrfToken('authenticate', $credentials['csrf_token']);
            $request->getSession()->set(Security::LAST_USERNAME, $credentials['username']);

            if (!$this->csrfTokenManager->isTokenValid($token)) {
                throw new InvalidCsrfTokenException();
            }
        }
        elseif ('app_consent' === $this->route) {

        }

        return $credentials;
    }

    /**
     * @param mixed                 $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return UserInterface|null
     */
    final public function getUser($credentials, UserProviderInterface $userProvider): UserInterface {
        if ('app_login' === $this->route) {
            $user = $userProvider->loadUserByUsername($credentials['username']);

            if (!$user) {
                // fail authentication with a custom error
                throw new CustomUserMessageAuthenticationException('Name could not be found.');
            }
        }
        elseif ('app_consent' === $this->route) {

        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user): bool {
        if ('app_login' === $this->route) {
            $encoder = $this->encoderFactory->getEncoder($user);
            $isValid = ($user->getUsername() === $credentials['username']) && $encoder->isPasswordValid($user->getPassword(), $credentials['password'], null);

            return $isValid;
        }

        return false;
    }


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        // For example : return new RedirectResponse($this->urlGenerator->generate('some_route'));
        throw new \Exception('TODO: provide a valid redirect inside ' . __FILE__);
    }

    protected function getLoginUrl() {
        return $this->urlGenerator->generate('app_login');
    }
}
