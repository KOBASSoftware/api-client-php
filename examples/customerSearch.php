<?php
$companyId   = 0; // Company ID
$staffId     = 0; //Staff ID
$staffSecret = ''; // Staff API Key

$signer = new \Kobas\Auth\Signer($companyId, $staffId, $staffSecret);
$client = new \Kobas\Client($signer);

$venues = $client->get('customer/search', ['email' => 'example@example.com']);