<?php

namespace Kobas\Request;

class Curl implements HttpRequest
{
	private $handle = null;

	public function __construct()
	{
		$this->handle = curl_init();
	}

	public function setUrl($url)
	{
		return $this->setOption(CURLOPT_URL, $url);
	}

	public function setOption($name, $value)
	{
		curl_setopt($this->handle, $name, $value);
		return $this;
	}

	public function execute()
	{
		return curl_exec($this->handle);
	}

	public function getInfo($name) 
	{
		return curl_getinfo($this->handle, $name);
	}

	public function close() 
	{
		curl_close($this->handle);
		return $this;
	}
}