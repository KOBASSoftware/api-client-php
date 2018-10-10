<?php

namespace Kobas\APIClient\Test\Provider;

use Kobas\APIClient\Auth\Provider;
use Kobas\APIClient\Exception\AuthenticationException;
use League\OAuth2\Client\Tool\QueryBuilderTrait;
use League\OAuth2\Client\Token;
use Mockery as m;

class ProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Provider
     */
    protected $provider;

    public function tearDown()
    {
        m::close();
        parent::tearDown();
    }


    public function testSetUrl()
    {
        $this->provider->setUrl('mock_url');
        $this->assertEquals($this->provider->getUrl(),  'mock_url');
    }

    public function testGetCompanyId() {
        $this->assertEquals(12345, $this->provider->getCompanyId());
    }

    /**
     * @expectedException \Kobas\APIClient\Exception\AuthenticationException
     **/
    public function testGetAccessToken() {
        $this->provider->getAccessToken();

        // Todo figure out how to mock this to get expected result.
        $fakeToken = m::mock('League\OAuth2\Client\Token');
        $fakeProvider = m::mock('Kobas\OAuth2\Client\Provider\Kobas');
        $fakeProvider->shouldReceive('getAccessToken')->andReturn($fakeToken);
    }

    protected function setUp()
    {
        $this->provider = new Provider(12345, 'mock_client_id', 'mock_client_secret', 'mock_scope');
    }


}