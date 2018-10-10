<?php

require '../vendor/autoload.php';

$companyId = 0000;// company id provided by Kobas
$clientId = '';// client id provided by Kobas
$clientSecret = '';// client secret provided by Kobas
$clientScope = ''; // client scope provided by Kobas

$provider = new \Kobas\APIClient\Auth\Provider($companyId, $clientId, $clientSecret, $clientScope);
$client = new \Kobas\APIClient\Client($provider);

$request = $client->get('customer/search', array('email' => 'example@example.com'));