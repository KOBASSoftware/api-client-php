<?php

namespace Kobas\APIClient\Exception;

/**
 * Class CurlException
 * @package Kobas\APIClient\Exception
 */
class CurlException extends \Exception
{

    /**
     * CurlException constructor.
     *
     * @param $message
     * @param $code
     */
    public function __construct($message, $code)
    {
        parent::__construct($message, $code);
    }
}
