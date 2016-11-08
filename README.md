KOBAS API Client
=============

Signs API requests and allows interaction via cURL methods.

## Example

```php
/**
 * Setup
 */
$companyId   = 0; // Company ID
$staffId     = 0; //Staff ID
$staffSecret = ''; // Staff API Key

$signer = new \Kobas\Auth\Signer($companyId, $staffId, $staffSecret);
$client = new \Kobas\Client($signer);

/**
 * Usage
 */
$venues = $client->get('data/venue');
```

## Client Functions

## get($route, $params = array(), $headers = array())
cURL Get Request

## post($route, $params = array(), $headers = array())
cURL Post Request

## put($route, $params = array(), $headers = array())
cURL Put Request

## delete($route, $params = array(), $headers = array())
cURL Delete Request

## setAPIBaseURL($url)
Allows over-riding the base URL (only really needed for development)

## setAPIVersion($version)
Allows over-riding of the API version. Might be useful in future?

## disableSSLVerification()
Disables SSL Verify Peer. Needed for development, should never be used in production