<?php

namespace App\Controller;

use App\Hydra\DTO\LoginFormDTO;
use App\Hydra\HydraException;
use App\Hydra\HydraLogin;
use App\Hydra\HydraLoginFactory;
use App\Identity\IdentityProviderInterface;
use App\Identity\UserNotFoundException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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

        //dump('before fetch login');
        try {
            $login = $this->fetchLogin($request);

            if ($login->isSkip()) {
                $response = $login->acceptLoginRequest();

                return new RedirectResponse($response->getRedirectTo(), 307); // redirect back to hydra
            }

            [$form, $isUserValid, $formData] = $this->handleForm($login->getChallenge(), $request);

            /** @var LoginFormDTO $formData */
            if ($formData && $isUserValid) {
                //dump('user check valid');
                //dump('doing accept Login');

                $response = $login->acceptLoginRequest($formData->getUsername(), $formData->isRemember(), 3600);

                return new RedirectResponse($response->getRedirectTo(), 307); // redirect back to hydra
            }
//            else {
//                // you could handle login rejection here
//            }

            return $this->render('security/login.html.twig',
                                 [
                                   'form' => $form->createView(),
                                 ]
            );
        } catch (HydraException $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     *
     * @return HydraLogin
     * @throws HydraException
     */
    private function fetchLogin(Request $request): HydraLogin {
        $challenge = $request->get('login_challenge');

        if (empty($challenge)) {
            $challenge = $request->request->get('form', [])['challenge'] ?? null;
        }

        return $this->loginFactory->fetchLoginRequest($challenge);
    }

    private function handleForm(string $challenge, Request $request): array {
        $isUserValid = false;
        $data        = null;

        $form = $this->buildForm($challenge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            assert($data instanceof LoginFormDTO);

            try {
                //dump('before user validation');
                $isUserValid = $this->identityProvider->verify([
                                                                 'username' => $data->getUsername(),
                                                                 'password' => $data->getPassword(),
                                                               ]
                );

                if (!$isUserValid) {
                    //dump('user is not valid');
                    $form->addError(new FormError('Credentials are not matched.'));
                }
            } catch (UserNotFoundException $e) {
                //dump('user is not found');
                $form->addError(new FormError('There is something wrong.', null, [], null, 'UserNotFound'));
            }
        }

        //dump('out of form handling');

        return [$form, $isUserValid, $data];
    }

    /**
     * @param string $login_challenge
     *
     * @return FormInterface
     * @see Customize form rendering
     *      https://symfony.com/doc/current/form/form_customization.html
     *
     */
    final public function buildForm(string $login_challenge): FormInterface {

        $defaultData = new LoginFormDTO();
        $defaultData->setChallenge($login_challenge);

        $formOptions = [
          'csrf_protection' => true,

          // the name of the hidden HTML field that stores the token
          'csrf_field_name' => '_csrf_token',

          // an arbitrary string used to generate the value of the token
          // using a different string for each form improves its security
          'csrf_token_id'   => 'authenticate',
        ];

        return $this->createFormBuilder($defaultData, $formOptions)
                    ->add('username', TextType::class)
                    ->add('password', PasswordType::class)
                    ->add('remember', CheckboxType::class,
                          [
                            'label'    => 'Remember me',
                            'required' => false,
                          ]
                    )
                    ->add('challenge', HiddenType::class)
                    ->add('submit', SubmitType::class, ['label' => 'Sign in'])
                    ->getForm();
    }

}
