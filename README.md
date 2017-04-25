KOBAS API Client
=============

Signs API requests and allows interaction via cURL methods.

## Example

```php
/**
 * Setup
 */
$companyId  = 0; // Company ID
$identifier = 'sid:?/imid:?'; // Identifier either sid: (staff id) or imid: (integration member id) followed by the id provided.
$secret     = '...'; // API Key

$signer = (new \Kobas\Auth\Signer($companyId, $identifier, $secret));
$client = (new \Kobas\Client($signer));

/**
 * Usage
 */
$response = $client->get('customer/search', ['email' => 'example@example.com']);
echo json_encode($response, JSON_PRETTY_PRINT);
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
