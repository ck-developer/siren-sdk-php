<?php

namespace Siren\Tests\HttpClient\Authentication;

use Psr\Http\Message\RequestInterface;
use Siren\HttpClient\Authentication\AccessToken;
use Siren\HttpClient\Authentication\Authentication;
use Siren\HttpClient\Authentication\TokenProvider;
use Siren\Tests\TestCase;

class AccessTokenTest extends TestCase
{
    public function testConstruct()
    {
        $accessToken = new AccessToken(
            'c07702ad-599c-3271-b3b0-33af71de4ff0',
            'Bearer',
            594029,
            \DateTime::createFromFormat('Y-m-d', '2019-09-04')
        );

        $this->assertEquals('c07702ad-599c-3271-b3b0-33af71de4ff0', $accessToken->getToken());
        $this->assertEquals('Bearer', $accessToken->getType());
        $this->assertEquals(594029, $accessToken->getExpireIn());
        $this->assertEquals(\DateTime::createFromFormat('Y-m-d', '2019-09-04'), $accessToken->getCreatedAt());
        $this->assertTrue($accessToken->isExpired());
    }
}