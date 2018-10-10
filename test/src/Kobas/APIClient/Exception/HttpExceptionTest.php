<?php

namespace Kobas\APIClient\Test\Provider;

use Kobas\APIClient\Exception\HttpException;

class HttpExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function tearDown()
    {
        parent::tearDown();
    }

    public function testException() {
        $exception = new HttpException('Something went wrong', 12345);

        $this->assertEquals('Something went wrong', $exception->getMessage());
        $this->assertEquals(12345, $exception->getCode());
    }
}