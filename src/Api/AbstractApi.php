<?php

namespace Siren\Api;

use Siren\Hydrator\ArrayHydrator;
use Siren\Hydrator\HydratorInterface;
use Http\Client\Common\PluginClient;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

abstract class AbstractApi
{
    /**
     * @var PluginClient
     */
    private $httpClient;

    /**
     * @var RequestFactoryInterface|null
     */
    private $requestFactory;

    /**
     * @var UriFactoryInterface|null
     */
    private $uriFactory;

    /**
     * @var ArrayHydrator|HydratorInterface
     */
    private $hydrator;

    /**
     * Siren constructor.
     *
     * @param PluginClient $httpClient
     * @param RequestFactoryInterface $requestFactory
     * @param UriFactoryInterface $uriFactory
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        PluginClient $httpClient,
        RequestFactoryInterface $requestFactory,
        UriFactoryInterface $uriFactory,
        HydratorInterface $hydrator
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->uriFactory = $uriFactory;
        $this->hydrator = $hydrator;
    }

    /**
     * @param string $method
     * @param string|UriInterface $uri
     * @param array $query
     *
     * @return ResponseInterface
     *
     * @throws \Http\Client\Exception
     */
    protected function sendRequest(string $method, $uri, array $query = [])
    {
        $uri = $this->uriFactory->createUri($uri)
            ->withQuery(\http_build_query($query))
        ;

        $request = $this->requestFactory->createRequest($method, $uri);

        return $this->httpClient->sendRequest($request);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return array|mixed
     *
     * @throws \Exception
     */
    protected function hydrate(ResponseInterface $response)
    {
        return $this->hydrator->hydrate($response);
    }
}
