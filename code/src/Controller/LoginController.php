<?php

namespace App\Controller;

use App\Hydra\DTO\CompletedRequest;
use App\Hydra\HydraException;
use App\Hydra\HydraLogin;
use App\Hydra\HydraLoginFactory;
use App\Identity\IdentityProviderInterface;
use App\Identity\UserNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class LoginController extends AbstractController {

    private $identityProvider;

    private $loginFactory;

    public function __construct(IdentityProviderInterface $identityProvider, HydraLoginFactory $loginFactory) {
        $this->identityProvider = $identityProvider;
        $this->loginFactory     = $loginFactory;
    }

    /**
     * @Route("/login", name="app_login", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     * @see  https://www.ory.sh/docs/oryos.13/hydra/oauth2
     *       Integrate hydra into existing application
     *
     * @see  https://symfony.com/doc/current/forms.html#creating-form-classes
     * @see  https://symfony.com/doc/current/form/unit_testing.html
     * @see  https://www.ory.sh/docs/hydra/sdk/api#get-a-login-request
     * @see  https://www.ory.sh/docs/hydra/sdk/api#schemaloginrequest
     *
     * @todo hydra login flow
     * @todo correct the form validation error
     */
    final public function loginFlow(Request $request): Response {
        $error          = null;
        $hydraException = null;
        $login          = null;

        dump('before fetch login');

        [$login, $hydraException] = $this->fetchLogin($request);

        if ($login) {
            if ($login->isSkipLogin()) {
                assert($login instanceof HydraLogin);
                dump('skip login, before accept login');
                [$response, $hydraException] = $this->acceptLogin($login, $login->getSubject());

                if ($response) {
                    assert($response instanceof CompletedRequest);

                    return new RedirectResponse($response->getRedirectTo(), 307); // redirect back to hydra
                }
            }


            dump('into form handling');
            [$form, $isUserValid, $username] = $this->handleForm($login->getChallenge(), $request);


            if ($isUserValid) {
                dump('user check valid');
                dump('doing accept Login');

                [$response, $hydraException] = $this->acceptLogin($login, $username, true, 3600);
                dump('success accept Login');
                if ($response) {
                    assert($response instanceof CompletedRequest);

                    return new RedirectResponse($response->getRedirectTo(), 307); // redirect back to hydra
                }
            }
        }

        /** @var HydraException $hydraException */
        if ($hydraException) {
            throw new HttpException(500, $hydraException->getMessage());
        }

        return $this->render('security/login.html.twig',
                             [
                               'form'           => $form->createView(),
                               'error'          => $error
                             ]
        );
    }

    /**
     * @param Request $request
     *
     * @return array [?LoginRequest, ?HydraException]
     */
    private function fetchLogin(Request $request): array {
        $challenge = $request->get('login_challenge');

        if (empty($challenge)) {
            $challenge = $request->request->get('form', [])['challenge'] ?? null;
        }

        try {
            return [$this->loginFactory->fetchLoginRequest($challenge), null];
        } catch (HydraException $e) {
            return [null, $e];
        }
    }

    /**
     * @param HydraLogin $login
     * @param string     $subject
     * @param bool|null  $remember
     * @param int|null   $remember_for
     *
     * @return array [?CompletedRequest, ?HydraException]
     *
     * @see  https://www.ory.sh/docs/hydra/sdk/api#accept-a-login-request
     */
    private function acceptLogin(HydraLogin $login, string $subject, ?bool $remember = null, ?int $remember_for = null): array {
        $options = ['subject' => $subject];

        if (null !== $remember) {
            $options['remember'] = $remember;
        }

        if (null !== $remember_for) {
            $options['remember_for'] = $remember_for;
        }

        try {
            return [$login->acceptLoginRequest($options), null];
        } catch (HydraException $e) {
            return [null, $e];
        }
    }

    private function handleForm(string $challenge, Request $request): array {
        $isUserValid = false;
        $username    = null;

        $form = $this->buildForm($challenge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump('going to submit form');

            $data     = $form->getData();
            $username = $data['username'] ?? null;
            $password = $data['password'] ?? null;

            try {
                dump('before user validation');
                $isUserValid = $this->identityProvider->verify(['username' => $username, 'password' => $password]);

                if (!$isUserValid) {
                    dump('user is not valid');
                    $form->addError(new FormError('Credentials are not matched.'));
                }
            } catch (UserNotFoundException $e) {
                dump('user is not found');
                $form->addError(new FormError('There is something wrong.', null, [], null, 'UserNotFound'));
            }
        }

        dump('out of form handling');
        return [$form, $isUserValid, $username];
    }

    /**
     * @param string|null $login_challenge
     *
     * @return FormInterface
     * @see Customize form rendering
     *      https://symfony.com/doc/current/form/form_customization.html
     *
     */
    final public function buildForm(?string $login_challenge): FormInterface {

        $defaultData = ['challenge' => $login_challenge];
        $formOptions = [
          'csrf_protection' => true,

          // the name of the hidden HTML field that stores the token
          'csrf_field_name' => '_csrf_token',

          // an arbitrary string used to generate the value of the token
          // using a different string for each form improves its security
          'csrf_token_id'   => 'authenticate',
        ];

        $form = $this->createFormBuilder($defaultData, $formOptions)
                     ->add('username', TextType::class)
                     ->add('password', PasswordType::class)
                     ->add('challenge', HiddenType::class)
                     ->add('submit', SubmitType::class, ['label' => 'Sign in'])
                     ->getForm();

        return $form;
    }

}
