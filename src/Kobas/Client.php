<?php

namespace Kobas;


use Kobas\Exception\CurlException;
use Kobas\Exception\HttpException;
use Kobas\Auth\Signer;
use Kobas\Request\Curl;
use Kobas\Request\HttpRequest;

/**
 * Class Client
 * @package Kobas
 */
class Client
{
    /**
     * @var string
     */
    protected $api_base_url = 'https://api.kobas.co.uk';

    /**
     * @var string
     */
    protected $version = 'v2';

    /**
     * @var bool
     */
    protected $ssl_verify_peer = true;

    /**
     * @var Signer
     */
    protected $signer;

    /**
     * @var Curl|HttpRequest|null
     */
    protected $request;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var array
     */
    protected $curl_options;

    /**
     * Client constructor.
     * @param Signer $signer
     * @param HttpRequest|null $request
     * @param array $headers
     * @param array $curl_options
     */
    public function __construct(Signer $signer, HttpRequest $request = null, $headers = array(), $curl_options = array())
    {
        $this->signer = $signer;
        if ($request == null) {
            $request = new Curl();
        }
        $this->request = $request;
        $this->headers = $headers;
        $this->curl_options = $curl_options;
    }

    /**
     * Allows you to set the API Base URL. Default value is 'https://api.kobas.co.uk'
     * @param $url
     */
    public function setAPIBaseURL($url)
    {
        $this->api_base_url = $url;
    }

    /**
     * Allows you to set the API version to use. Default value is 'v2'
     * @param $version
     */
    public function setAPIVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Allows you to disable SSL verify peer (useful for development, don't use in production)
     */
    public function disableSSLVerification()
    {
        $this->ssl_verify_peer = false;
    }


    /**
     * @param $route
     * @param array $params
     * @param array $headers
     * @return mixed
     * @throws HttpException
     * @throws CurlException
     */
    public function get($route, array $params = array(), array $headers = array())
    {
        return $this->call('GET', $route, $params, $headers);
    }


    /**
     * @param $route
     * @param array $params
     * @param array $headers
     * @return mixed
     * @throws HttpException
     * @throws CurlException
     */
    public function post($route, array $params = array(), array $headers = array())
    {
        return $this->call('POST', $route, $params, $headers);
    }


    /**
     * @param $route
     * @param array $params
     * @param array $headers
     * @return mixed
     * @throws HttpException
     * @throws CurlException
     */
    public function put($route, array $params = array(), array $headers = array())
    {
        return $this->call('PUT', $route, $params, $headers);
    }


    /**
     * @param $route
     * @param array $params
     * @param array $headers
     * @return mixed
     * @throws HttpException
     * @throws CurlException
     */
    public function delete($route, array $params = array(), array $headers = array())
    {
        return $this->call('DELETE', $route, $params, $headers);
    }

    /**
     * @param $http_method
     * @param $route
     * @param array $params
     * @param array $headers
     * @return mixed
     * @throws HttpException
     * @throws CurlException
     */
    protected function call($http_method, $route, array $params = array(), array $headers = array())
    {
        $url = $this->api_base_url . '/';
        if (!empty($this->version)) {
            $url .= $this->version . '/';
        }

        $url .= $route;

        $this->request
            ->init()
            ->setOption(CURLOPT_CUSTOMREQUEST, strtoupper($http_method))
            ->setOption(CURLOPT_RETURNTRANSFER, true)
            ->setOption(CURLOPT_FOLLOWLOCATION, true)
            ->setOption(CURLOPT_ENCODING, '');

        foreach ($this->curl_options as $option => $value) {
            $this->request->setOption($option, $value);
        }

        if (!$this->ssl_verify_peer) {
            $this->request->setOption(CURLOPT_SSL_VERIFYPEER, false);
        }

        $headers = array_merge($this->headers, $headers);

        $headers['Content-Type'] = 'application/json';
        $headers['User-Agent'] = 'Kobas API V2 Client';

        switch ($http_method) {
            case 'POST':
                $this->request->setOption(CURLOPT_POSTFIELDS, json_encode($params));
                break;
            case 'DELETE':
            case 'PUT':
                $this->request->setOption(CURLOPT_POSTFIELDS, json_encode($params));
                break;
            case 'GET':
                if (count($params)) {
                    $url .= "?" . http_build_query($params);
                    $params = array();
                }
                break;
        }

        $this->request->setUrl($url);

        $headers = $this->signer->signRequest($http_method, $url, $headers, $params);

        $this->request->setOption(CURLOPT_HTTPHEADER, $headers);


        $result = $this->request->execute();

        if ($this->request->getErrorNumber()) {
            throw new CurlException($this->request->getErrorMessage(), $this->request->getErrorNumber());
        }

        $last_response = $this->request->getInfo(CURLINFO_HTTP_CODE);

        $this->request->close();

        if ($last_response >= 400) {
            throw new HttpException($last_response, json_encode(json_decode($result, true), true));
        }

        return json_decode($result, true);
    }
}
