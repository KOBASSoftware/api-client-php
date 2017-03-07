<?php

namespace spec\Kobas;

use Kobas\Auth\Signer;
use Kobas\Client;
use Kobas\Exception\HttpException;
use Kobas\Request\Curl;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ClientSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Client::class);
    }

    function let(Signer $signer, Curl $request)
    {
        $this->beConstructedWith($signer, $request);
    }

    function it_throws_exception(Curl $request)
    {
        $request->setOption(Argument::type('int'), Argument::any())->willReturn($request);
        $request->setUrl('https://api.kobas.co.uk/v2/data/venue/2')->willReturn($request);
        $request->execute()->willReturn(null);

        $request->getInfo(Argument::type('int'))->willReturn(404);

        $this->shouldThrow(new HttpException(404, null))->during('get', ["data/venue/2"]);
    }
}
