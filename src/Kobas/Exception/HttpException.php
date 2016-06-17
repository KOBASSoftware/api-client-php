<?php

namespace Kobas\Exception;


class HttpException extends \Exception
{
	protected $data;

	public function __construct($http_code, $data, $message = '')
	{
		parent::__construct($message, $http_code);
		$this->setData($data);
	}

	private function setData($data)
	{
		$this->data = $data;
	}

	public function getData()
	{
		return $this->data;
	}
}