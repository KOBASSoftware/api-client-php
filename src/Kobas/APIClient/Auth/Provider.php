<?php

namespace Kobas\APIClient\Auth;

use GuzzleHttp\Exception\ConnectException;
use Kobas\APIClient\Exception\AuthenticationException;
use Kobas\APIClient\Exception\CurlException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Kobas\OAuth2\Client\Provider\Kobas as OAuthProvider;

/**
 * Class OAuthSigner
 *
 * @package Kobas\APIClient\Auth
 */
class Provider
{
    /**
     * @var AccessToken[]
     */
    protected static $tokens;
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
    protected $url;

    /**
     * OAuthSigner constructor.
     *
     * @param int $companyId
     * @param string $clientId
     * @param string $clientSecret
     * @param string $scopes
     * @param string $url
     */
    public function __construct($companyId, $clientId, $clientSecret, $scopes, $url = 'https://oauth.kobas.co.uk')
    {
        $this->companyId = (int)$companyId;
        $this->clientId = (string)$clientId;
        $this->clientSecret = (string)$clientSecret;
        $this->scopes = (string)$scopes;
        $this->url = (string)$url;
    }

    /**
     * @return string
     */
    public function getUrl()
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
    public function getCompanyId()
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

        if (!is_array(self::$tokens) ||
            !array_key_exists($provider->companyId, self::$tokens) ||
            !self::$tokens[$provider->companyId] instanceof AccessToken ||
            self::$tokens[$provider->companyId]->hasExpired()
        ) {
            try {
                self::$tokens[$provider->companyId] = $provider->getAccessToken(
                    'client_credentials',
                    ['scope' => $this->scopes]
                );
            } catch (IdentityProviderException $e) {
                throw new AuthenticationException($e->getMessage(), $e->getCode());
            } catch (ConnectException $e) {
                throw new CurlException($e->getMessage(), $e->getCode());
            }
        }

        return self::$tokens[$provider->companyId]->getToken();
    }

    /**
     * @return OAuthProvider
     */
    public function getProvider()
    {
        $provider = new OAuthProvider([
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'companyId' => $this->companyId,
            'url' => $this->url
        ]);
        return $provider;
    }
}
