<?php

namespace Kobas\APIClient\Test\Provider;

use Kobas\APIClient\Auth\Provider;
use Kobas\APIClient\Client;

use Mockery as m;

class ClientTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Provider
     */
    protected $provider;

    /** @var Client */
    protected $client;

    public function tearDown()
    {
        m::close();
        parent::tearDown();
    }

    public function testSetAPIBaseUrl() {
        $this->client->setAPIBaseURL('https://123434.3434.3434');
        $this->assertEquals('https://123434.3434.3434', $this->client->getApiBaseUrl());
    }

    public function testSetAPIVersion() {
        $this->client->setAPIVersion(22);
        $this->assertEquals(22, $this->client->getVersion());
    }

    protected function setUp()
    {
        $this->provider = new Provider(12345, 'mock_client_id', 'mock_client_secret', 'mock_scope');
        $this->client = new Client($this->provider);
    }
}