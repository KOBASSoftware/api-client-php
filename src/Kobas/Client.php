<?php

namespace Kobas;


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
    protected $api_url;
    /**
     * @var string
     */
    protected $api_base_url = 'https://api.kobas.co.uk';
//    protected $api_base_url = 'https://api.kobas.dev';
    /**
     * @var string
     */
    protected $version = 'v2';
    /**
     * @var Signer
     */
    protected $signer;
    /**
     * @var bool
     */
    protected $ssl_verify_peer = true;

    /**
     * Client constructor.
     * @param Signer $signer
     * @param HttpRequest|null $request
     */
    public function __construct(Signer $signer, HttpRequest $request = null)
    {
        $this->signer  = $signer;
        if($request == null){
            $request = new Curl();
        }
        $this->request = $request;
        $this->api_url = $this->api_base_url . '/' . $this->version . '/';
    }

    /**
     * @param $route
     * @param array $params
     * @param array $headers
     * @return mixed
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
     */
    protected function call($http_method, $route, array $params = array(), array $headers = array())
    {
        $url = $this->api_url . trim($route, '/');

        $this->request
            ->setOption(CURLOPT_CUSTOMREQUEST, strtoupper($http_method))
            ->setOption(CURLOPT_RETURNTRANSFER, true)
            ->setOption(CURLOPT_FOLLOWLOCATION, true)
            ->setOption(CURLOPT_ENCODING, '');

        if (!$this->ssl_verify_peer) {
            $this->request->setOption(CURLOPT_SSL_VERIFYPEER, false);
        }

        $headers['Content-Type'] = 'application/json';

        switch ($http_method) {
            case 'POST':
                $this->request->setOption(CURLOPT_POSTFIELDS, json_encode($params));
                break;
            case 'DELETE':
            case 'PUT':
                $this->request->setOption(CURLOPT_POSTFIELDS, http_build_query($params));
                break;
            case 'GET':
                if (count($params)) {
                    $url .= "?" . http_build_query($params);
                    $params = [];
                }
                break;
        }

        $this->request->setUrl($url);

        $headers = $this->signer->signRequest($http_method, $url, $headers, $params);

        $this->request->setOption(CURLOPT_HTTPHEADER, $headers);

        $result        = $this->request->execute();
        $last_response = $this->request->getInfo(CURLINFO_HTTP_CODE);
        if ($last_response >= 400) {
            throw new HttpException($last_response, json_encode($result, true));
        }

        $this->request->close();

        return json_decode($result, true);
    }
}