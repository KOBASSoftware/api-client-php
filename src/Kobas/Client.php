<?php

namespace Kobas;


use Kobas\Auth\Signer;

class Client
{
	protected $api_url = 'https://api.kobas.co.uk/v2';
	protected $signer;

	public function __construct(Signer $signer)
	{
		$this->signer = $signer;
	}

	protected function call($http_method, array $headers, $url, $params)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($http_method));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		curl_setopt($ch, CURLOPT_COOKIE, 'XDEBUG_SESSION_START=php;');


		$headers['Content-Type'] = 'application/json';


		switch ($http_method)
		{
			case 'POST':
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
				break;
			case 'DELETE':
			case 'PUT':
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
				break;
			case 'GET':
				$url .= "?" . http_build_query($params);
				$params = [];
				break;
		}

		curl_setopt($ch, CURLOPT_URL, $url);

		$this->signer->signRequest($http_method, $url, $headers, $params);
	}
}