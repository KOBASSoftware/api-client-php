<?php

namespace Kobas\APIClient\Request;

/**
 * Interface HttpRequest
 * @package Kobas\APIClient\Request
 */
interface HttpRequest
{
    /**
     * @return $this
     */
    public function init();

    /**
     * @param $url
     * @return $this
     */
    public function setUrl($url);

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setOption($name, $value);

    /**
     * @return mixed
     */
    public function execute();

    /**
     * @param $name
     * @return mixed
     */
    public function getInfo($name);


    /**
     * @return mixed
     */
    public function getErrorNumber();

    /**
     * @return mixed
     */
    public function getErrorMessage();


    /**
     * @return $this
     */
    public function close();
}
