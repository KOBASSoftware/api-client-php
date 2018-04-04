<?php

namespace Kobas\Exception;


/**
 * Class HttpException
 * @package Kobas\Exception
 */
class HttpException extends \Exception
{
    /**
     * @var
     */
    protected $data;

    /**
     * HttpException constructor.
     * @param $http_code
     * @param string $message
     */
    public function __construct($http_code, $message = '')
    {
        parent::__construct($message, $http_code);
    }
}