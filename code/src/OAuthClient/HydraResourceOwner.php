<?php

namespace App\OAuthClient;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * Class HydraResourceOwner
 * @package ChrisHemmings\OAuth2\Client\Provider
 *
 * @see https://www.ory.sh/hydra/docs/reference/api#openid-connect-userinfo
 * @see https://www.ory.sh/hydra/docs/reference/api#schemauserinforesponse
 *
 *      {
            "birthdate": "string",
            "email": "string",
            "email_verified": true,
            "family_name": "string",
            "gender": "string",
            "given_name": "string",
            "locale": "string",
            "middle_name": "string",
            "name": "string",
            "nickname": "string",
            "phone_number": "string",
            "phone_number_verified": true,
            "picture": "string",
            "preferred_username": "string",
            "profile": "string",
            "sub": "string",
            "updated_at": 0,
            "website": "string",
            "zoneinfo": "string"
            }
 */
class HydraResourceOwner implements ResourceOwnerInterface {
    /**
     * Raw response
     *
     * @var
     */
    protected $response;

    /**
     * Creates new resource owner.
     *
     * @param array $response
     */
    public function __construct(array $response) {
        $this->response = $response;
    }

    /**
     * Get resource owner id
     *
     * @return string|null
     */
    public function getId(): ?string  {
        return $this->response['sub'] ?: null;
    }

    /**
     * Return all of the owner details available as an array.
     *
     * @return array
     */
    public function toArray(): array {
        return $this->response;
    }

    /**
     * Get emailaddress
     *
     * @return string|null
     */
    public function getEmail() : ?string {
        return $this->response['email'] ?: null;
    }

    /**
     * Get email verified
     *
     * @return bool
     */
    public function isEmailVerified() :bool{
        return (bool)$this->response['email_verified'] ?: false;
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName(): ?string  {
        return $this->response['name'] ?: null;
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getPreferredName(): ?string  {
        return $this->response['preferred_username'] ?: null;
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getZoneInfo(): ?string {
        return $this->response['zoneinfo'] ?: null;
    }
}