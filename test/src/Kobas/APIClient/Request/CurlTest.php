<?php

namespace Kobas\APIClient\Test\Provider;

use Kobas\APIClient\Exception\CurlException;
use Kobas\APIClient\Request\Curl;

class CurlTest extends \PHPUnit\Framework\TestCase
{
    /** @var Curl */
    protected $request;

    public function tearDown()
    {
        parent::tearDown();
    }

    protected function setUp()
    {
        $this->request = new Curl();
    }

    public function test() {
        $request = $this->request->init();
        $this->assertInstanceOf('Kobas\APIClient\Request\Curl', $request);
        $request->setUrl('https://fakefake.fake.fake');
        $url = $request->getInfo(CURLINFO_EFFECTIVE_URL);
        $this->assertEquals('https://fakefake.fake.fake', $url);
        $request->close();
        $this->assertEquals(null, $request->getHandle());
    }
}