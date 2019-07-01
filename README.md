# Kobas API Client
[![Latest Version](https://img.shields.io/github/release/KOBASSoftware/api-client-php.svg?style=flat-square)](https://github.com/KOBASSoftware/api-client-php/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/KOBASSoftware/api-client-php/master.svg?style=flat-square)](https://travis-ci.org/KOBASSoftware/api-client-php)
[![Documentation](https://img.shields.io/badge/documentation-passing-brightgreen.svg?style=flat-square)](https://api-doc.kobas.co.uk/)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/KOBASSoftware/api-client-php.svg?style=flat-square)](https://scrutinizer-ci.com/g/KOBASSoftware/api-client-php/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/KOBASSoftware/api-client-php.svg?style=flat-square)](https://scrutinizer-ci.com/g/KOBASSoftware/api-client-php)
[![Total Downloads](https://img.shields.io/packagist/dt/kobas/api-client.svg?style=flat-square)](https://packagist.org/packages/KOBASSoftware/api-client-php)

Signs API requests and allows interaction via cURL methods.

## Example

```php
/**
 * Setup
 */
$companyId = 0000;// company id provided by Kobas
$clientId = '';// client id provided by Kobas
$clientSecret = '';// client secret provided by Kobas
$clientScope = ''; // client scope provided by Kobas

$provider = new \Kobas\APIClient\Auth\Provider($companyId, $clientId, $clientSecret, $clientScope);
$client = new \Kobas\APIClient\Client($provider);

/**
 * Usage
 */
$response = $client->get('customer/search', ['email' => 'example@example.com']);
echo json_encode($response, JSON_PRETTY_PRINT);
```

## Client Functions

## get($route, $params = array(), $headers = array())
Sends a HTTP GET Request to the route provided.

## post($route, $params = array(), $headers = array())
Sends a HTTP POST Request to the route provided.

## put($route, $params = array(), $headers = array())
Sends a HTTP PUT Request to the route provided.

## delete($route, $params = array(), $headers = array())
Sends a HTTP DELETE Request to the route provided.

## getRequestInfo()
Returns the result of `curl_getinfo()`on the last request made as an array.

## setAPIBaseURL($url)
Allows over-riding the base URL (only really needed for development)

## setAPIVersion($version)
Allows over-riding of the API version. Might be useful in future?



