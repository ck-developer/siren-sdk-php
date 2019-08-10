<?php

namespace Siren\Tests\HttpClient;

use Siren\HttpClient\Authentication\AccessToken;
use Siren\HttpClient\Authentication\TokenProvider;
use Siren\Tests\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\NullAdapter;

class TokenProviderTest extends TestCase
{
    public function testGetAccessTokenWithoutCache()
    {
        $provider = new TokenProvider(
            'DjrTR3BcmxomLIDbiPqBN4WN9zca',
            'ICEdwU_Ct7CsaShm65PqSX7Earsa',
            null,
            $this->getMockedClient()
        );

        $this->mockResponseFromPath(__DIR__ . '/../../mocks/authentication/success.txt');

        $accessToken = $provider->getAccessToken();

        $request = $this->getLastRequest();

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/token?grant_type=client_credentials', $request->getRequestTarget());
        $this->assertEquals('DjrTR3BcmxomLIDbiPqBN4WN9zca:ICEdwU_Ct7CsaShm65PqSX7Earsa', $request->getUri()->getUserInfo());

        $this->assertInstanceOf(AccessToken::class, $accessToken);
        $this->assertEquals('c07702ad-599c-3271-b3b0-33af71de4ff0', $accessToken->getToken());
        $this->assertEquals('Bearer', $accessToken->getType());
    }

    public function testGetAccessTokenWithCache()
    {
        $provider = new TokenProvider(
            'DjrTR3BcmxomLIDbiPqBN4WN9zca',
            'ICEdwU_Ct7CsaShm65PqSX7Earsa',
            new NullAdapter(),
            $this->getMockedClient()
        );

        $this->mockResponseFromPath(__DIR__ . '/../../mocks/authentication/success.txt');

        $accessToken = $provider->getAccessToken();

        $request = $this->getLastRequest();

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/token?grant_type=client_credentials', $request->getRequestTarget());
        $this->assertEquals('DjrTR3BcmxomLIDbiPqBN4WN9zca:ICEdwU_Ct7CsaShm65PqSX7Earsa', $request->getUri()->getUserInfo());

        $this->assertInstanceOf(AccessToken::class, $accessToken);
        $this->assertEquals('c07702ad-599c-3271-b3b0-33af71de4ff0', $accessToken->getToken());
        $this->assertEquals('Bearer', $accessToken->getType());
    }

    public function testGetAccessTokenFromCache()
    {
        $pool = new ArrayAdapter();

        $item = $pool->getItem('siren_access_token');
        $item->set(new AccessToken('c07702ad-599c-3271-b3b0-33af71de4ff1', 'Bearer', 594029));

        $pool->save($item);

        $provider = new TokenProvider(
            'DjrTR3BcmxomLIDbiPqBN4WN9zca',
            'ICEdwU_Ct7CsaShm65PqSX7Earsa',
            $pool,
            $this->getMockedClient()
        );

        $accessToken = $provider->getAccessToken();

        $this->assertInstanceOf(AccessToken::class, $accessToken);
        $this->assertEquals('c07702ad-599c-3271-b3b0-33af71de4ff1', $accessToken->getToken());
        $this->assertEquals('Bearer', $accessToken->getType());
    }
}
