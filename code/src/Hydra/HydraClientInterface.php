<?php

namespace App\Hydra;

use App\Hydra\DTO\CompletedRequest;
use App\Hydra\DTO\ConsentRequest;
use App\Hydra\DTO\LoginRequest;

interface HydraClientInterface {

    /**
     * @param string $challenge
     *
     * @return LoginRequest
     *
     * @throws HydraException
     *
     * @see https://www.ory.sh/docs/hydra/sdk/api#schemaloginrequest
     * @see https://www.ory.sh/docs/hydra/sdk/api#get-a-login-request
     */
    public function fetchLoginRequest(string $challenge): LoginRequest;

    /**
     * @param string $challenge
     * @param array  $options
     *
     * @return CompletedRequest
     *
     * @throws HydraException
     *
     * @see   https://www.ory.sh/docs/hydra/sdk/api#accept-a-login-request
     * @see   https://www.ory.sh/docs/hydra/sdk/api#schemaacceptloginrequest
     */
    public function acceptLoginRequest(string $challenge, array $options = []): CompletedRequest;

    /**
     * @param string $challenge
     * @param array  $options
     *
     * @return CompletedRequest
     *
     * @throws HydraException
     *
     * @see https://www.ory.sh/docs/hydra/sdk/api#reject-a-login-request
     * @see https://www.ory.sh/docs/hydra/sdk/api#schemarejectrequest
     * @see https://www.ory.sh/docs/hydra/sdk/api#schemacompletedrequest
     */
    public function rejectLoginRequest(string $challenge, array $options = []): CompletedRequest;

    /**
     * @param string $challenge
     *
     * @return LoginRequest
     *
     * @throws HydraException
     *
     * @see https://www.ory.sh/docs/hydra/sdk/api#schemaloginrequest
     * @see https://www.ory.sh/docs/hydra/sdk/api#get-a-login-request
     */
    public function fetchConsentRequest(string $challenge): ConsentRequest;

    /**
     * @param string $challenge
     * @param array  $options
     *
     * @return CompletedRequest
     *
     * @throws HydraException
     *
     * @see   https://www.ory.sh/docs/hydra/sdk/api#accept-a-consent-request
     * @see   https://www.ory.sh/docs/hydra/sdk/api#schemaacceptconsentrequest
     */
    public function acceptConsentRequest(string $challenge, array $options = []): CompletedRequest;

    /**
     * @param string $challenge
     * @param array  $options
     *
     * @return CompletedRequest
     *
     * @throws HydraException
     *
     * @see https://www.ory.sh/docs/hydra/sdk/api#reject-a-consent-request
     * @see https://www.ory.sh/docs/hydra/sdk/api#schemarejectrequest
     * @see https://www.ory.sh/docs/hydra/sdk/api#schemacompletedrequest
     */
    public function rejectConsentRequest(string $challenge, array $options = []): CompletedRequest;
}