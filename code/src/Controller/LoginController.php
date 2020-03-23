<?php

namespace App\Controller;

use App\Service\HydraClientInterface;
use App\Service\HydraLogin;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class LoginController extends AbstractController {

    /**
     * @var HydraClientInterface
     */
    private $hydraLogin;

    /**
     * LoginController constructor.
     *
     * @param HydraLogin $hydraLogin
     * @param Security   $security
     */
    public function __construct(HydraLogin $hydraLogin, Security $security) {
        $this->hydraLogin = $hydraLogin;
    }


    /**
     * @Route("/login", name="app_login")
     * @param ServerRequestInterface $request
     *
     * @return Response
     */
    final public function login(ServerRequestInterface $request, AuthenticationUtils $authenticationUtils): Response {
        $params          = $request->getQueryParams();
        $login_challenge = $params['login_challenge'] ?? null;

        $this->hydraLogin->startLogin($login_challenge);


        if ($this->hydraLogin->needLogin()) {
            $form = $this->createForm($authenticationUtils, $login_challenge);
            $user = $this->processForm($form, $request);

            if (null === $user) {
                return $this->render('task/new.html.twig', [
                                                           'form' => $form->createView(),
                                                         ]
                );
            }
        }

        $redirect = $this->hydraLogin->acceptLogin();

        return new RedirectResponse($redirect);
    }


    final private function processForm(FormInterface $form, ServerRequestInterface $request) {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $task = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            // $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($task);
            // $entityManager->flush();

            return 'aaa';
        }

        return null;
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    final public function logout(): void {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    final private function createForm(AuthenticationUtils $authenticationUtils, string $login_challenge): Response {
        //        if ($this->getUser()) {
        //            //return $this->redirectToRoute('target_path');
        //        }


        $error        = $authenticationUtils->getLastAuthenticationError(); // get the login error if there is one
        $lastUsername = $authenticationUtils->getLastUsername(); // last username entered by the user

        $formData    = ['username' => $lastUsername, 'login_challenge' => $login_challenge];
        $formOptions = [
          'csrf_protection' => true,

          // the name of the hidden HTML field that stores the token
          'csrf_field_name' => '_csrf_token',

          // an arbitrary string used to generate the value of the token
          // using a different string for each form improves its security
          'csrf_token_id'   => 'authenticate',
        ];

        $form = $this->createFormBuilder($formData, $formOptions)
                     ->add('username', TextType::class)
                     ->add('password', PasswordType::class)
                     ->add('login_challenge', HiddenType::class)
                     ->add('submit', SubmitType::class, ['label' => 'Sign in'])
                     ->getForm();

        return $this->render('security/login.html.twig', [
                                                         'form'          => $form->createView(),
                                                         'last_username' => $lastUsername,
                                                         'error'         => $error,
                                                       ]
        );
    }
}
