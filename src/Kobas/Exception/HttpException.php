<?php

namespace Kobas\Exception;


class HttpException extends \Exception
{
	public function __construct($http_code, $message = '')
	{
		parent::__construct($message, $http_code);
	}
}