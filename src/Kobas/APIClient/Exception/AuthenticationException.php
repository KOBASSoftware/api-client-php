<?php

namespace Kobas\APIClient\Exception;


/**
 * Class AuthenticationException
 * @package Kobas\APIClient\Exception
 */
class AuthenticationException extends \Exception
{
    /**
     * @var
     */
    protected $data;

    /**
     * HttpException constructor.
     * @param string $message
     * @param $code
     */
    public function __construct($message, $code)
    {
        parent::__construct($message, $code);
    }
}