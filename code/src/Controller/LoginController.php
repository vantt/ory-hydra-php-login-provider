<?php

namespace App\Controller;

use App\Form\LoginForm;
use App\Security\IdentityProviderInterface;
use App\Service\HydraClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class LoginController extends AbstractController {

    /**
     * @Route("/login", name="app_login", methods={"GET","POST"})
     *
     * @param Request                   $request
     *
     * @param IdentityProviderInterface $identityProvider
     *
     * @return Response
     * @see  https://symfony.com/doc/current/forms.html#creating-form-classes
     * @see  https://symfony.com/doc/current/form/unit_testing.html
     *
     * @todo view the Form
     * @todo submit and collect data
     * @todo do authentication
     * @todo csrf protect
     * @todo hydra login flow
     */
    final public function showForm(Request $request, IdentityProviderInterface $identityProvider): Response {
        $error     = null;
        $challenge = $request->query->get('login_challenge');
        $form      = $this->buildForm($challenge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data     = $form->getData();
            $username = $data['username'] ?? null;
            $password = $data['password'] ?? null;
            $isValid  = $identityProvider->verify(['username' => $username, 'password' => $password]);

            if ($isValid) {
                return new RedirectResponse('/consent');
            }
        }

        return $this->render('security/login.html.twig', ['form' => $form->createView(), 'error' => $error]);
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
