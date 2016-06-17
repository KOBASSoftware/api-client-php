<?php

namespace Kobas;


use Kobas\Exception\HttpException;
use Kobas\Auth\Signer;
use Kobas\Request\HttpRequest;

class Client
{
//	protected $api_url = 'https://api.kobas.dev/v2';
	protected $api_url = 'https://api.kobas.co.uk/v2';
	protected $signer;

	public function __construct(Signer $signer, HttpRequest $request)
	{
		$this->signer 	= $signer;
		$this->request	= $request;
	}

	protected function call($http_method, $service, array $params = array(), array $headers = array())
	{
		$url = $this->api_url . '/' . trim($service, '/');

		$this->request
			->setOption(CURLOPT_CUSTOMREQUEST, strtoupper($http_method))
			->setOption(CURLOPT_RETURNTRANSFER, true)
			->setOption(CURLOPT_SSL_VERIFYPEER, false)
			->setOption(CURLOPT_FOLLOWLOCATION, true)
			->setOption(CURLOPT_ENCODING, '')
			->setOption(CURLOPT_COOKIE, 'XDEBUG_SESSION_START=php;')
		;


		$headers['Content-Type'] = 'application/json';


		switch ($http_method)
		{
			case 'POST':
				$this->request->setOption(CURLOPT_POSTFIELDS, json_encode($params));
				break;
			case 'DELETE':
			case 'PUT':
			$this->request->setOption(CURLOPT_POSTFIELDS, http_build_query($params));
				break;
			case 'GET':
				if (count($params))
				{
					$url .= "?" . http_build_query($params);
					$params = [];
				}
				break;
		}

		$this->request->setUrl($url);

		$headers = $this->signer->signRequest($http_method, $url, $headers, $params);

		print_r($headers);
		$this->request->setOption(CURLOPT_HTTPHEADER, $headers);

		$result = $this->request->execute();
		$last_response = $this->request->getInfo(CURLINFO_HTTP_CODE);
		if ($last_response >= 400)
		{
			throw new HttpException($last_response);
		}

		$this->request->close();

		return json_decode($result, true);
	}

	public function getVenues($id = 0)
	{
		$service = 'venue';
		if ($id)
		{
			$service .= '/' . $id;
		}
		return $this->call('GET', $service);
	}

	public function createCustomer($data)
	{
		$service = 'loyalty/customer';

		$fields = [
			'customer',
			'title',
			'firstname',
			'surname',
			'date_of_birth',
			'gender',
			'address_1',
			'address_2',
			'city',
			'postcode',
			'county',
			'email',
			'email_optin',
			'mobile',
			'mobile_optin',
			'venue',
			];

		foreach($data as $key => $val)
		{
			if (!in_array($key, $fields))
			{
				unset($data[$key]);
			}
		}

		return $this->call('POST', $service, $data);
	}
}