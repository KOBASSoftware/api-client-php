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
    
    function it_requests_venues(Curl $request)
    {
        $data = [
            [
                'id' => 123,
                'value' => 'value'
            ]
        ];
        $request->setOption(Argument::type('int'), Argument::any())->willReturn($request);
        $request->setUrl('https://api.kobas.co.uk/v2/venue')->willReturn($request);
        $request->execute()->willReturn(json_encode($data));
        $request->getInfo(Argument::type('int'))->willReturn(200);
        $request->close()->willReturn($request);

        $this->getVenues()->shouldReturn($data);
    }

    function it_requests_one_venue(Curl $request)
    {
        $data = [
                'id' => 123,
                'value' => 'value'
        ];
        $request->setOption(Argument::type('int'), Argument::any())->willReturn($request);
        $request->setUrl('https://api.kobas.co.uk/v2/venue/2')->willReturn($request);
        $request->execute()->willReturn(json_encode($data));
        $request->getInfo(Argument::type('int'))->willReturn(200);
        $request->close()->willReturn($request);

        $this->getVenues(2)->shouldReturn($data);
    }

    function it_throws_exception(Curl $request)
    {
        $request->setOption(Argument::type('int'), Argument::any())->willReturn($request);
        $request->setUrl('https://api.kobas.co.uk/v2/venue/2')->willReturn($request);
        $request->execute()->willReturn(null);

        $request->getInfo(Argument::type('int'))->willReturn(404);

        $this->shouldThrow(new HttpException(404))->during('getVenues', [2]);
    }
}
