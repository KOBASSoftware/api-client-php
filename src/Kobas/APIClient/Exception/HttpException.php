<?php

namespace Kobas\APIClient\Exception;

/**
 * Class HttpException
 * @package Kobas\APIClient\Exception
 */
class HttpException extends \Exception
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
