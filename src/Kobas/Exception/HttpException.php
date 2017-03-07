<?php

namespace Kobas\Exception;


class HttpException extends \Exception
{
    protected $data;

    public function __construct($http_code, $message = '')
    {
        parent::__construct($message, $http_code);
    }
}