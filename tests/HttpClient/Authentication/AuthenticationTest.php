<?php

namespace Siren\Tests\HttpClient\Authentication;

use Psr\Http\Message\RequestInterface;
use Siren\HttpClient\Authentication\AccessToken;
use Siren\HttpClient\Authentication\Authentication;
use Siren\HttpClient\Authentication\TokenProvider;
use Siren\Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public function testAuthentication()
    {
        $tokenProvider = $this->createMock(TokenProvider::class);

        $tokenProvider
            ->expects($this->any())
            ->method('getAccessToken')
            ->willReturn(new AccessToken(
                'c07702ad-599c-3271-b3b0-33af71de4ff0',
                'Bearer',
                594029,
                \DateTime::createFromFormat('Y-m-d', '2019-09-04')
            ));

        $authentication = new Authentication($tokenProvider);

        $request = $authentication->authenticate($this->createRequest('GET', '/'));

        $this->assertTrue($request->hasHeader('Authorization'));
        $this->assertEquals('Bearer c07702ad-599c-3271-b3b0-33af71de4ff0', $request->getHeaderLine('Authorization'));
    }
}