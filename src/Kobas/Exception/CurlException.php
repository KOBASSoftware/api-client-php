<?php

namespace Kobas\Exception;


/**
 * Class CurlException
 * @package Kobas\Exception
 */
class CurlException extends \Exception
{

    public function __construct($message, $code)
    {
        parent::__construct($message, $code);
    }
}