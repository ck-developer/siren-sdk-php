<?php

namespace Siren\HttpClient;

use Http\Client\Common\Plugin;
use Http\Client\Common\PluginClient;
use Http\Client\Common\PluginClientFactory;
use Http\Discovery\HttpAsyncClientDiscovery;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Siren\HttpClient\Authentication\Authentication;
use Siren\HttpClient\Authentication\TokenProvider;

class Configurator
{
    /**
     * @var string
     */
    private $clientKey;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var UriFactoryInterface
     */
    private $uriFactory;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var Plugin[]
     */
    private $plugins;

    public function __construct(string $clientKey, string $clientSecret)
    {
        $this->clientKey = $clientKey;
        $this->clientSecret = $clientSecret;
    }

    public function setHttpClient(ClientInterface $httpAdapter): self
    {
        $this->httpClient = $httpAdapter;

        return $this;
    }

    /**
     * @param RequestFactoryInterface $requestFactory
     *
     * @return self
     */
    public function setRequestFactory(RequestFactoryInterface $requestFactory): self
    {
        $this->requestFactory = $requestFactory;

        return $this;
    }

    /**
     * @param UriFactoryInterface $uriFactory
     *
     * @return self
     */
    public function setUriFactory(UriFactoryInterface $uriFactory): self
    {
        $this->uriFactory = $uriFactory;

        return $this;
    }


    public function addPlugin(Plugin $plugin): self
    {
        $this->plugins[get_class($plugin)] = $plugin;

        return $this;
    }

    public function createConfiguredClient(): PluginClient
    {
        $this->plugins = [];
        $this->httpClient = $this->httpClient ?? HttpAsyncClientDiscovery::find();

        $this
            ->addPlugin(
                new Plugin\BaseUriPlugin($this->getUriFactory()->createUri('https://api.insee.fr/entreprises/sirene/V3'))
            )
            ->addPlugin(
                new Plugin\HeaderDefaultsPlugin([
                    'Accept' => 'application/json',
                    'User-Agent' => 'insee-sdk-php (https://github.com/ck-developer/insee-sdk-php)',
                ])
            )
            ->addPlugin(
                new Plugin\ContentLengthPlugin()
            )
            ->addPlugin(new Plugin\AuthenticationPlugin(
                new Authentication(new TokenProvider($this->clientKey, $this->clientSecret))
            ))
        ;

        return (new PluginClientFactory())->createClient(
            $this->httpClient,
            $this->plugins,
            ['client_name' => 'Insee Sirene']
        );
    }

    public function getUriFactory(): UriFactoryInterface
    {
        return $this->uriFactory = $this->uriFactory ?? Psr17FactoryDiscovery::findUrlFactory();
    }

    public function getRequestFactory(): RequestFactoryInterface
    {
        return $this->requestFactory = $this->requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
    }
}
