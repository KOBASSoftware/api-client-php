<?php

require '../vendor/autoload.php';

$companyId  = 2716; // Company ID
$identifier = 'imid:1'; // Identifier
$secret     = '...'; // API Key

$signer = (new \Kobas\Auth\Signer($companyId, $identifier, $secret));
$client = (new \Kobas\Client($signer));

$providedExample = json_decode('{"customer":{"id": 1665454,"occurredAt": "2017-02-01 10:00:00","isFirstSightingOfUser": true,"user": {"id": 55566,"emailAddress": "abc@def.com","firstName": "john","lastName": "farrimond","dateOfBirth": "1985-01-01","gender": "M","location": "leyland, lancashire","postcode": "PR25 3GR","mobilePhoneNumber": "07712345679","firstSeenAt": "2017-02-01 10:00:00","lastSeenAt": "2017-02-01 10:00:00","numLogins": 1,"dataSource": "facebook","dataSourceId": "14545654446"},"venue": {"id": 5554,"name": "bob\'s cafe","customerRef": "1"}}}', true);

$request = $client->post('integrations/wifi-login/wireless-social', $providedExample);
