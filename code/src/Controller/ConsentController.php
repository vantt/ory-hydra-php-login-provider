<?php

namespace App\Controller;

use App\Hydra\DTO\ConsentFormDTO;
use App\Hydra\DTO\ConsentRequest;
use App\Hydra\HydraConsent;
use App\Hydra\HydraConsentFactory;
use App\Hydra\HydraException;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;


class ConsentController extends AbstractController {
    /**
     * @var HydraConsentFactory
     */
    private $consentFactory;

    public function __construct(HydraConsentFactory $consentFactory) {
        $this->consentFactory   = $consentFactory;
    }

    /**
     * @Route("/consent", name="app_consent", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     * @see  https://www.ory.sh/docs/oryos.13/hydra/oauth2
     *       Integrate hydra into existing application
     *
     * @see  https://symfony.com/doc/current/forms.html#creating-form-classes
     * @see  https://symfony.com/doc/current/form/unit_testing.html
     *
     * @see  https://www.ory.sh/docs/hydra/sdk/api#get-consent-request-information
     * @see  https://www.ory.sh/docs/hydra/sdk/api#schemaconsentrequest
     *
     * @todo hydra consent flow
     * @todo correct the form validation error
     */
    final public function consentFlow(Request $request): Response {
        $error          = null;
        $hydraException = null;
        $consent        = null;

        //dump('before fetch consent');

        try {
            $consent = $this->fetchConsent($request);

            if ($consent->isSkip()) {
                //dump('skip login, before accept login');
                $response = $consent->acceptConsentRequest();
                return new RedirectResponse($response->getRedirectTo(), 307); // redirect back to hydra
            }

            $form = $this->buildForm($consent->getConsentRequest());
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var ConsentFormDTO $data */
                $data = $form->getData();
                $data->setApproval($form->getClickedButton()->getName());

                if ($data->isApproved()) {
                    $response = $consent->acceptConsentRequest($data->getGrantScope(), [], [], $data->isRemember());
                }
                else {
                    $response = $consent->rejectConsentRequest();
                }
                return new RedirectResponse($response->getRedirectTo(), 307); // redirect back to hydra
            }

            $client = $consent->getConsentRequest()->getClient();

            return $this->render('security/consent.html.twig',
                                 [
                                   'form'    => $form->createView(),
                                   'subject' => $consent->getConsentRequest()->getSubject(),
                                   'client'  => $client->getClientName() ?: $client->getClientId(),
                                 ]
            );
        } catch (HydraException $e) {
            throw new HttpException(500, $hydraException->getMessage());
        }
    }

    /**
     * @Route("/consent_test", name="app_consent_test", methods={"GET","POST"})
     */
    public function testFlow(Request $request) {
        $data = [
          "challenge" => 'kjhfkajsdaskjdhflasdf',

          "skip"        => true,

          // The user-id of the already authenticated user - only set if skip is true
          "subject"     => 'vantt',

          // The initial OAuth 2.0 request url
          "request_url" => 'http://adfadsf.com/adfaf',

          "login_challenge" => 'adksjfjkshgfkajshdfgsaf',

          "login_session_id"                => 'asldjfhlasjkfhalskfdjhalf',

          // The OAuth 2.0 client that initiated the request
          "client"                          => [
            'client_id'   => 'anphabe_app',
            'client_name' => 'The Anphabe TestClient',
          ],
          "requested_access_token_audience" => 'asfsadfsfasfasfsf',
          // The OAuth 2.0 Scope requested by the client,
          "requested_scope"                 => ['user.account', 'user.profile', 'user.picture'],

          // Information on the OpenID Connect request - only required to process if your UI should support these values.
          "oidc_context"                    => ['something'],

          // Context is an optional object which can hold arbitrary data. The data will be made available when fetching the
          // consent request under the "context" field. This is useful in scenarios where login and consent endpoints share
          // data.
          "context"                         => ['some_otherthing'],
        ];

        $consent = ConsentRequest::fromArray($data);
        $client  = $consent->getClient();

        $form = $this->buildForm($consent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data->setApproval($form->getClickedButton()->getName());
        }

        return $this->render('security/consent.html.twig',
                             [
                               'form'    => $form->createView(),
                               'subject' => $consent->getSubject(),
                               'client'  => $client->getClientName() ?: $client->getClientId(),
                             ]
        );

    }

    /**
     * @param ConsentRequest $consentRequest
     *
     * @return FormInterface
     * @see Customize form rendering
     *      https://symfony.com/doc/current/form/form_customization.html
     */
    final public function buildForm(ConsentRequest $consentRequest): FormInterface {
        $defaultData = new ConsentFormDTO();
        $defaultData->setChallenge($consentRequest->getChallenge());

        $formOptions = [
          'csrf_protection' => true,

          // the name of the hidden HTML field that stores the token
          'csrf_field_name' => '_csrf_token',

          // an arbitrary string used to generate the value of the token
          // using a different string for each form improves its security
          'csrf_token_id'   => 'consent',
        ];

        $choices = array_combine($consentRequest->getRequestedScope(), $consentRequest->getRequestedScope());

        return $this->createFormBuilder($defaultData, $formOptions)
                    ->add('grant_scope', ChoiceType::class,
                          [
                            'label'    => 'Grant Scopes',
                            'required' => true,
                            'choices'  => $choices,
                            'expanded' => true,
                            'multiple' => true,
                          ]
                    )
                    ->add('remember', CheckboxType::class,
                          [
                            'label'    => 'Do not ask me again',
                            'required' => false,
                          ]
                    )
                    ->add('deny', SubmitType::class, ['label' => 'Deny Access'])
                    ->add('allow', SubmitType::class, ['label' => 'Allow Access'])
                    ->add('challenge', HiddenType::class)
                    ->getForm();

    }

    /**
     * @param Request $request
     *
     * @return HydraConsent
     * @throws HydraException
     */
    private function fetchConsent(Request $request): HydraConsent {
        $challenge = $request->get('consent_challenge');

        if (empty($challenge)) {
            $challenge = $request->request->get('form', [])['challenge'] ?? null;
        }

        return $this->consentFactory->fetchConsentRequest($challenge);
    }
}
