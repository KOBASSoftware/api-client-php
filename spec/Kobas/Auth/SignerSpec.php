<?php

namespace spec\Kobas\Auth;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SignerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Kobas\Auth\Signer');
    }

    function let()
    {
        $this->beConstructedWith(Argument::type('int'), Argument::type('int'), Argument::type('string'));
    }

    function it_signs_get_request()
    {
        $url = 'https://api.kobas.co.uk/v2/venue/3?field=id,name';

        $result = $this->signRequest('GET', $url, ['Content-Type' => 'application/json']);

        $result->shouldHaveValueLike('Authorization: Bearer ');
        $result->shouldHaveValueLike('X-Kbs-Date: ');

        $this->getService()->shouldReturn('venue/3');
        $this->getMethod()->shouldReturn('GET');
    }

    function it_signs_post_request()
    {
        $url = 'https://api.kobas.co.uk/v2/venue';

        $result = $this->signRequest('POST', $url, ['Content-Type' => 'application/json'],
            ['name' => 'New venue', 'description' => 'A good description']);

        $result->shouldHaveValueLike('Authorization: Bearer ');
        $result->shouldHaveValueLike('X-Kbs-Date: ');

        $this->getService()->shouldReturn('venue');
        $this->getMethod()->shouldReturn('POST');

        $this->getParams()->shouldHaveKey('name');
        $this->getParams()->shouldHaveKey('description');

    }

    public function getMatchers()
    {
        return [
            'haveKey' => function ($subject, $key) {
                return array_key_exists($key, $subject);
            },
            'haveValueLike' => function ($subject, $value) {
                $found = false;
                foreach ($subject as $val) {
                    if (strstr($val, $value)) {
                        $found = true;
                    }
                }

                return $found;
            },
        ];
    }
}
