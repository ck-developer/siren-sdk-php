<?php

namespace Siren\HttpClient\Authentication;

use Http\Discovery\HttpAsyncClientDiscovery;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * Class TokenProvider.
 */
class TokenProvider
{
    /**
     * @var \Http\Client\HttpAsyncClient|ClientInterface
     */
    private $httpClient;

    /**
     * @var RequestFactoryInterface|\Psr\Http\Message\UriFactoryInterface
     */
    private $requestFactory;
    /**
     * @var CacheItemPoolInterface
     */
    private $itemPool;
    /**
     * @var string
     */
    private $clientKey;
    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var \Psr\Http\Message\UriFactoryInterface
     */
    private $uriFactory;
    /**
     * @var CacheItemPoolInterface|null
     */
    private $cacheItemPool;

    /**
     * TokenProvider constructor.
     */
    public function __construct(
        string $clientKey,
        string $clientSecret,
        CacheItemPoolInterface $cacheItemPool = null,
        ClientInterface $httpClient = null,
        UriFactoryInterface $uriFactory = null,
        RequestFactoryInterface $requestFactory = null
    ) {
        $this->clientKey = $clientKey;
        $this->clientSecret = $clientSecret;
        $this->cacheItemPool = $cacheItemPool;
        $this->httpClient = $httpClient ?? HttpAsyncClientDiscovery::find();
        $this->uriFactory = $uriFactory ?? Psr17FactoryDiscovery::findUrlFactory();
        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
    }

    /**
     * @return AccessToken
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    protected function generateAccessToken()
    {
        $uri = $this->uriFactory->createUri('https://api.insee.fr/token')
            ->withUserInfo($this->clientKey, $this->clientSecret)
            ->withQuery('grant_type=client_credentials')
        ;

        $request = $this->requestFactory->createRequest('POST', $uri);

        $response = $this->httpClient->sendRequest($request);

        $data = json_decode($response->getBody()->getContents(), true);

        return AccessToken::fromArray($data);
    }

    /**
     * @return AccessToken
     *
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function getAccessToken()
    {
        if (null == $this->cacheItemPool) {
            return $this->generateAccessToken();
        }

        $itemAccessToken = $this->cacheItemPool->getItem('siren_access_token');

        if (true == $itemAccessToken->isHit()) {
            return $itemAccessToken->get();
        }

        $accessToken = $this->generateAccessToken();

        $itemAccessToken
            ->expiresAt($accessToken->getExpireAt())
            ->set($accessToken)
        ;

        $this->cacheItemPool->save($itemAccessToken);

        return $accessToken;
    }
}
