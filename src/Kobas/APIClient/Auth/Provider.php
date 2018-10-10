<?php

namespace Kobas\APIClient\Auth;

use GuzzleHttp\Exception\ConnectException;
use Kobas\APIClient\Exception\AuthenticationException;
use Kobas\APIClient\Exception\CurlException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;

/**
 * Class OAuthSigner
 *
 * @package Kobas\APIClient\Auth
 */
class Provider
{
    /**
     * @var AccessToken
     */
    protected static $token;
    /**
     * @var int
     */
    protected $companyId;
    /**
     * @var string
     */
    protected $clientId;
    /**
     * @var string
     */
    protected $clientSecret;
    /**
     * @var string
     */
    protected $scopes;
    /**
     * @var string
     */
    protected $url = 'https://oauth.kobas.co.uk';

    /**
     * OAuthSigner constructor.
     *
     * @param int $companyId
     * @param string $clientId
     * @param string $clientSecret
     * @param string $scopes
     */
    public function __construct(int $companyId, string $clientId, string $clientSecret, string $scopes)
    {
        $this->companyId = $companyId;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->scopes = $scopes;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return int
     */
    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    /**
     * @return string
     * @throws AuthenticationException
     * @throws CurlException
     */
    public function getAccessToken()
    {
        $provider = $this->getProvider();

        if (!self::$token instanceof AccessToken || self::$token->hasExpired()) {
            try {
                self::$token = $provider->getAccessToken('client_credentials', ['scope' => $this->scopes]);
            } catch (IdentityProviderException $e) {
                throw new AuthenticationException($e->getMessage(), $e->getCode());
            } catch (ConnectException $e) {
                throw new CurlException($e->getMessage(), $e->getCode());
            }
        }

        return self::$token->getToken();
    }

    /**
     * @return \Kobas\OAuth2\Client\Provider\Kobas
     */
    public function getProvider()
    {
        $provider = new \Kobas\OAuth2\Client\Provider\Kobas([
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'companyId' => $this->companyId,
            'url' => $this->url
        ]);
        return $provider;
    }
}
