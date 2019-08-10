<?php

namespace Siren\Tests\HttpClient;

use Http\Client\Common\Plugin\AuthenticationPlugin;
use Siren\HttpClient\Authentication\Authentication;
use Siren\HttpClient\Authentication\TokenProvider;
use Siren\HttpClient\Configurator;
use Siren\Tests\TestCase;
use Http\Client\Common\Plugin\BaseUriPlugin;
use Http\Client\Common\Plugin\ContentLengthPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\PluginClient;
use Http\Discovery\Psr17FactoryDiscovery;

class ConfiguratorTest extends TestCase
{
    public function testConstruct()
    {
        $configurator = new Configurator('client_key', 'client_secret');
        $configurator
            ->setHttpClient($this->getMockedClient())
            ->setRequestFactory($requestFactory = Psr17FactoryDiscovery::findRequestFactory())
            ->setUriFactory($uriFactory = Psr17FactoryDiscovery::findUrlFactory())
        ;

        $this->assertInstanceOf(PluginClient::class, $client = $configurator->createConfiguredClient());
        $this->assertSame($requestFactory, $configurator->getRequestFactory());
        $this->assertSame($uriFactory, $configurator->getUriFactory());

        $reflectedClient = new \ReflectionObject($client);
        $reflectedPluginsProperty = $reflectedClient->getProperty('plugins');
        $reflectedPluginsProperty->setAccessible(true);

        $plugins = $reflectedPluginsProperty->getValue($client);

        $this->assertArrayHasKey(BaseUriPlugin::class, $plugins);
        $this->assertEquals(
            $plugins[BaseUriPlugin::class],
            new BaseUriPlugin($uriFactory->createUri('https://api.insee.fr/entreprises/sirene/V3'))
        );

        $this->assertArrayHasKey(HeaderDefaultsPlugin::class, $plugins);
        $this->assertEquals(
            $plugins[HeaderDefaultsPlugin::class],
            new HeaderDefaultsPlugin([
                'Accept' => 'application/json',
                'User-Agent' => 'insee-sdk-php (https://github.com/ck-developer/insee-sdk-php)',
            ])
        );

        $this->assertArrayHasKey(ContentLengthPlugin::class, $plugins);
        $this->assertEquals(
            $plugins[ContentLengthPlugin::class],
            new ContentLengthPlugin()
        );

        $this->assertArrayHasKey(AuthenticationPlugin::class, $plugins);
        $this->assertEquals(
            $plugins[AuthenticationPlugin::class],
            new AuthenticationPlugin(new Authentication(new TokenProvider('client_key', 'client_secret')))
        );
    }
}
