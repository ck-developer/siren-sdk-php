<?php

namespace Siren\Tests\Api;

use Http\Client\Common\PluginClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Mock\Client;
use Siren\Api\EstablishmentApi;
use Siren\Api\UniteLegalApi;
use Siren\Hydrator\ArrayHydrator;
use Psr\Http\Message\RequestInterface;
use Siren\Tests\TestCase;

class EstablishmentApiTest extends TestCase
{
    public function testSearch()
    {
        $api = new EstablishmentApi(
            new PluginClient($this->getMockedClient()),
            Psr17FactoryDiscovery::findRequestFactory(),
            Psr17FactoryDiscovery::findUrlFactory(),
            new ArrayHydrator()
        );

        $this->mockResponseFromPath(__DIR__.'/../../mocks/establishment/search/success.txt');

        $result = $api->search([
            'q' => 'siren:849967211',
        ]);

        $request = $this->getLastRequest();

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals(
            '/siret?q=siren%3A849967211',
            $request->getRequestTarget()
        );

        $this->assertTrue(is_array($result));
    }

    public function testGet()
    {
        $api = new EstablishmentApi(
            new PluginClient($this->getMockedClient()),
            Psr17FactoryDiscovery::findRequestFactory(),
            Psr17FactoryDiscovery::findUrlFactory(),
            new ArrayHydrator()
        );

        $this->mockResponseFromPath(__DIR__.'/../../mocks/establishment/get/success.txt');

        $result = $api->get('84996721100012');

        /** @var RequestInterface $request */
        $request = $this->getLastRequest();

        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/siret/84996721100012', $request->getUri()->__toString());

        $this->assertTrue(is_array($result));
    }
}
