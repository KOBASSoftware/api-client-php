<?php

namespace Kobas\Request;


/**
 * Class Curl
 * @package Kobas\Request
 */
class Curl implements HttpRequest
{
    /**
     * @var null
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
     * @return $this|Curl
     */
    public function setUrl($url)
    {
        return $this->setOption(CURLOPT_URL, $url);
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setOption($name, $value)
    {
        curl_setopt($this->handle, $name, $value);
        return $this;
    }

    /**
     * @return mixed
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
    public function getErrorNumber() {
        return curl_errno($this->handle);
    }

    /**
     * @return mixed|string
     */
    public function getErrorMessage() {
        return curl_error($this->handle);
    }

    /**
     * @return $this
     */
    public function close()
    {
        curl_close($this->handle);
        $this->handle = null;

        return $this;
    }
}