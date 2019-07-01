<?php

namespace Kobas\APIClient\Request;

/**
 * Class Curl
 *
 * @package Kobas\APIClient\Request
 */
class Curl implements HttpRequest
{
    /**
     * @var null|false|resource
     */
    private $handle = null;

    /**
     * @return $this
     */
    public function init()
    {
        if (is_null($this->handle)) {
            $this->handle = curl_init();
        }

        return $this;
    }

    /**
     * @param $url
     * @return Curl
     */
    public function setUrl($url)
    {
        return $this->setOption(CURLOPT_URL, $url);
    }

    /**
     * @param $name
     * @param $value
     * @return Curl
     */
    public function setOption($name, $value)
    {
        curl_setopt($this->handle, $name, $value);
        return $this;
    }


    /**
     * @return false| resource
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @return bool|string
     */
    public function execute()
    {
        return curl_exec($this->handle);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getInfo($name)
    {
        return curl_getinfo($this->handle, $name);
    }

    /**
     * @return int|mixed
     */
    public function getErrorNumber()
    {
        return curl_errno($this->handle);
    }

    /**
     * @return mixed|string
     */
    public function getErrorMessage()
    {
        return curl_error($this->handle);
    }

    /**
     * @return Curl
     */
    public function close()
    {
        curl_close($this->handle);
        $this->handle = null;

        return $this;
    }
}
