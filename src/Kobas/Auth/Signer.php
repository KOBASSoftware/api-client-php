<?php

namespace Kobas\Auth;

use Kobas\Exception;

class Signer
{
	protected $method;
	protected $parsed_url;
	protected $service;
	protected $headers;
	protected $lc_headers;
	protected $request_timestamp;
	protected $secret;
	protected $params;


	protected $company_id;
	protected $staff_id;

	protected $region = 'uk-lon-1';
	protected $terminator = 'kbs_request';
	protected $auth_type = 'Bearer';
	protected $signed_headers = array();
	protected $signature;

	public function __construct($company_id, $staff_id, $secret)
	{
		$this->setCompanyId($company_id);
		$this->setStaffId($staff_id);
		$this->setSecret($secret);
	}

	public function signRequest($method, $url, array $signed_headers, array $data = array())
	{
		$parsed_url = parse_url($url);
		$parsed_url['query'] = isset($parsed_url['query']) ? $this->rfc3986Decode($parsed_url['query']) : '';
		$time = time();

		$this->setMethod($method);
		$this->setHeaders($signed_headers);
		$this->setParams($data);

		$signed_headers['X-Kbs-Date'] = $this->requestDateTime($time);

		$signature = $this->signature($time);

		$signed_headers['Authorization'] = $this->authorization($time, $signature);

		$headers = [];
		foreach($this->signed_headers as $key => $value)
		{
			$headers[] = $key . ': ' . $value;
		}

		return $headers;
	}

	/**
	 * Get the signature for this request
	 * @param float $time
	 * @return string
	 */
	protected function signature($time)
	{
		$k_date        = $this->hmac('KBS3' . $this->secret, date("Ymd", $time));
		$k_region      = $this->hmac($k_date, $this->region);
		$k_service     = $this->hmac($k_region, $this->service);
		$k_credentials = $this->hmac($k_service, $this->terminator);
		return $this->hmac($k_credentials, $this->stringToSign($time));
	}

	/**
	 * Generates the authorization string to use for the request.
	 * @param float $time
	 * @param string $signature
	 * @return string
	 * @internal param string $client_id
	 */
	protected function authorization($time, $signature)
	{
		return $this->auth_type .
		' Credential=' . $this->getCompanyId() . '-' . $this->getStaffId() .
		'/' . $this->credentialScope($time) . ',' .
		'SignedHeaders=' . implode(';', $this->signed_headers) . ',' .
		'Signature=' . $this->hex16($signature);
	}

	/**
	 * Get the string to sign
	 * @param float $time
	 * @return string
	 */
	protected function stringToSign($time)
	{
		$string_to_sign = $this->auth_type . "\n";
		$string_to_sign .= $this->requestDateTime($time) . "\n";
		$string_to_sign .= $this->credentialScope($time) . "\n";
		$string_to_sign .= $this->hex16($this->hash($this->canonicalRequest()));
		return $string_to_sign;
	}

	/**
	 * Get the date in ISO8601 format
	 * @param $time
	 * @return string
	 */
	protected function requestDateTime($time)
	{
		return gmdate('Ymd\THis\Z', $time);
	}

	/**
	 * Get the credential scope
	 * @param float $time
	 * @return string
	 */
	protected function credentialScope($time)
	{
		return gmdate('Ymd', $time) . '/' .
		$this->region . '/' .
		$this->service . '/' .
		$this->terminator;
	}

	/**
	 * Get the canonical request string
	 * @return string
	 */
	protected function canonicalRequest()
	{
		$canonical_request = $this->method . "\n";
		$canonical_request .= $this->canonicalUri($this->parsed_url['path']) . "\n";
		$canonical_request .= $this->canonicalQueryString($this->parsed_url['query']) . "\n";
		$canonical_request .= $this->canonicalHeaders($this->headers) . "\n";
		$canonical_request .= $this->payloadHash($this->params);

		return $canonical_request;
	}

	/**
	 * Hash of the http request body
	 * @param array $params
	 * @return string
	 */
	protected function payloadHash($params)
	{
		$string = (is_array($params) ? http_build_query($params) : $params);
		return $this->hex16($this->hash($string));
	}

	/**
	 * The request URI path.
	 * @param string $path
	 * @return string The request URI path.
	 */
	protected function canonicalUri($path) {
		if (strlen($path) > 0)
		{
			return $path;
		}
		return '/';
	}

	/**
	 * Generate the canonical query string
	 * @param string $query
	 * @return string
	 */
	protected function canonicalQueryString($query)
	{
		$query_params = explode("&", $query);
		usort($query_params, 'strcmp');

		$canonical = array();

		foreach ($query_params as &$kv)
		{
			if ($kv == '')
			{
				continue;
			}
			list($key, $value) = explode('=', $kv);
			$canonical[] = $this->rfc3986Encode($key) . '=' . $this->rfc3986Encode($value);
		}

		return implode('&', $canonical);
	}

	/**
	 * Encode the value according to RFC 3986.
	 * @todo $string could be an Array type
	 * @param string $string
	 * @return string
	 */
	protected function rfc3986Encode($string)
	{
		$string = rawurlencode($string);
		return str_replace('%7E', '~', $string);
	}

	/**
	 * Decode the value according to RFC 3986.
	 * @param string $string
	 * @return string
	 */
	protected function rfc3986Decode($string)
	{
		$string = str_replace('~', '%7E', $string);
		return rawurldecode($string);
	}

	/**
	 * Generate the canonical headers
	 * @param array $headers
	 * @return string
	 */
	protected function canonicalHeaders($headers)
	{
		uksort($headers, 'strnatcasecmp');
		$headers = $this->getFilteredHeaders($headers);
		$canonical_headers = '';
		$signed_headers = array();
		// Add headers to request and compute the string to sign
		foreach ($headers as $k => $v)
		{
			// Strip line breaks and remove consecutive spaces. Services collapse whitespace in signature calculation
			$v = preg_replace('/\s+/', ' ', trim($v));
			$canonical_headers .= strtolower($k) . ':' . $v . "\n";
			$signed_headers[] = strtolower($k);
		}
		$this->signed_headers = $signed_headers;
		return $canonical_headers . "\n" . implode(';', $signed_headers);
	}

	/**
	 * If we already have signed headers then only include those keys in the
	 * calculation
	 * @param array $headers
	 * @return array
	 */
	protected function getFilteredHeaders($headers)
	{
		if (count($this->signed_headers) == 0)
		{
			return $headers;
		}
		foreach ($headers as $k => $v)
		{
			if (!in_array(strtolower($k), $this->signed_headers))
			{
				unset($headers[$k]);
			}
		}
		return $headers;
	}

	/**
	 * SHA-256 hashes the string.
	 * @param $string
	 * @return string
	 */
	protected function hash($string)
	{
		return hash('sha256', $string, true);
	}

	/**
	 * Hex16-pack the data.
	 * @param string $value
	 * @return string
	 */
	protected function hex16($value)
	{
		$result = unpack('H*', $value);
		return reset($result);
	}

	/**
	 * Applies HMAC SHA-256 encryption to the string, salted by the key.
	 * @param $key
	 * @param $string
	 * @return string
	 */
	protected function hmac($key, $string)
	{
		return hash_hmac('sha256', $string, $key, true);
	}

	/**
	 * @return mixed
	 */
	public function getSecret()
	{
		return $this->secret;
	}

	/**
	 * @param mixed $secret
	 * @return $this
	 */
	public function setSecret($secret)
	{
		$this->secret = $secret;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * @param mixed $params
	 * @return $this
	 */
	public function setParams(array $params)
	{
		$this->sortArray($params);
		$this->params = $params;
		return $this;
	}

	protected function sortArray (&$array, $sort_flags = SORT_REGULAR)
	{
		if (!is_array($array)) return false;
		ksort($array, $sort_flags);
		foreach ($array as &$arr) {
			$this->sortArray($arr, $sort_flags);
		}
		return true;
	}

	/**
	 * Gets a header value by key. Lowercases the key for convenience
	 * @param string $key
	 * @return mixed
	 */
	protected function getHeader($key)
	{
		if (isset($this->headers[$key]))
		{
			return $this->headers[$key];
		}
		$key = strtolower($key);
		if (isset($this->lc_headers[$key]))
		{
			return $this->lc_headers[$key];
		}
		return null;
	}

	public function setSignature($signature)
	{
		$this->signature = $signature;
		return $this;
	}

	public function getSignature()
	{
		return $this->signature;
	}

	/**
	 * @return string
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * @param string $method
	 * @return $this
	 */
	public function setMethod($method)
	{
		$this->method = strtoupper($method);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getService()
	{
		return $this->service;
	}

	/**
	 * @param string $service
	 * @return $this
	 */
	public function setService($service)
	{
		$this->service = $service;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * @param array $headers
	 * @return $this
	 */
	public function setHeaders($headers)
	{
		if (array_key_exists('Authorization', $headers))
		{
			unset($headers['Authorization']);
		}
		$this->headers = $headers;
		$this->lc_headers = array_change_key_case($headers);
		return $this;
	}

	/**
	 * @return null
	 */
	public function getCompanyId()
	{
		return $this->company_id;
	}

	/**
	 * @param null $company_id
	 * @return $this
	 */
	public function setCompanyId($company_id)
	{
		$this->company_id = $company_id;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getStaffId()
	{
		return $this->staff_id;
	}

	/**
	 * @param mixed $staff_id
	 * @return $this
	 */
	public function setStaffId($staff_id)
	{
		$this->staff_id = $staff_id;
		return $this;
	}
}
