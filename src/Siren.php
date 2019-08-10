<?php

namespace Siren;

use Http\Client\Common\PluginClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Siren\HttpClient\Configurator;
use Siren\Hydrator\ArrayHydrator;
use Siren\Hydrator\HydratorInterface;

class Siren
{
    /**
     * @var PluginClient
     */
    private $httpClient;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var RequestFactoryInterface
     */
    private $uriFactory;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * Siren constructor.
     *
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        Configurator $configurator,
        HydratorInterface $hydrator = null
    ) {
        $this->httpClient = $configurator->createConfiguredClient();
        $this->uriFactory = $configurator->getUriFactory();
        $this->requestFactory = $configurator->getRequestFactory();
        $this->hydrator = $hydrator ?? new ArrayHydrator();
    }

    /**
     * @return Siren
     */
    public static function create(string $clientKey, string $clientSecret)
    {
        return new self(new Configurator($clientKey, $clientSecret));
    }

    public function unitLegal(): Api\UniteLegalApi
    {
        return new Api\UniteLegalApi($this->httpClient, $this->requestFactory, $this->uriFactory, $this->hydrator);
    }

    public function establishment(): Api\EstablishmentApi
    {
        return new Api\EstablishmentApi($this->httpClient, $this->requestFactory, $this->uriFactory, $this->hydrator);
    }
}
