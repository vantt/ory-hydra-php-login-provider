<?php

namespace App\Controller;

use App\Form\LoginForm;
use App\Service\HydraClientInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class LoginController extends AbstractController {

    /**
     * @var HydraClientInterface
     */
    private $hydraLogin;

    /**
     * @Route("/login", methods="GET", name="app_login")
     * @param ServerRequestInterface $request
     *
     * @return Response
     * @see https://symfony.com/doc/current/forms.html#creating-form-classes
     */
    final public function showLogin(ServerRequestInterface $request): Response {
        $params          = $request->getQueryParams();
        $login_challenge = $params['login_challenge'] ?? null;

        $form = $this->createForm(LoginForm::class, ['challenge' => $login_challenge]);
        return $this->render('security/login.html.twig', [
          'form' => $form->createView(),
        ]);
    }

}
