<?php

namespace App\Controller;

use App\Service\HydraClient;
use Nyholm\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class LoginController extends AbstractController {

    /**
     * @var HydraClient
     */
    private $hydra;

    /**
     * LoginController constructor.
     *
     * @param HydraClient $hydraClient
     */
    public function __construct(HydraClient $hydraClient) {
        $this->hydra = $hydraClient;
    }


    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    final public function login(ServerRequestInterface $request): Response {
        $params          = $request->getQueryParams();
        $login_challenge = $params['login_challenge'] ?? null;
        $loginInfo       = $this->hydra->fetchLogin($login_challenge);

        return new JsonResponse($loginInfo);
    }

    private function fetchLoginInfo(string $loginChallenge) {

    }


    private function doLogin(string $loginChallange) {

    }

    /**
     * @Route("/logout", name="app_logout")
     */
    final public function logout(): void {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
