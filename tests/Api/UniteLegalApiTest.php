<?php

namespace Siren\Tests\Api;

use Http\Client\Common\PluginClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Mock\Client;
use Siren\Api\UniteLegalApi;
use Siren\Hydrator\ArrayHydrator;
use Psr\Http\Message\RequestInterface;
use Siren\Tests\TestCase;

class UniteLegalApiTest extends TestCase
{
    public function testSearch()
    {
        $api = new UniteLegalApi(
            new PluginClient($this->getMockedClient()),
            Psr17FactoryDiscovery::findRequestFactory(),
            Psr17FactoryDiscovery::findUrlFactory(),
            new ArrayHydrator()
        );

        $this->mockResponseFromPath(__DIR__.'/../../mocks/unitelegal/search/success.txt');

        $result = $api->search([
            'q' => 'periode(nomUniteLegale:khedhiri) AND periode(categorieJuridiqueUniteLegale:1000)',
        ]);

        $request = $this->getLastRequest();

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals(
            '/siren?q=periode%28nomUniteLegale%3Akhedhiri%29+AND+periode%28categorieJuridiqueUniteLegale%3A1000%29',
            $request->getRequestTarget()
        );

        $this->assertTrue(is_array($result));
    }

    public function testGet()
    {
        $api = new UniteLegalApi(
            new PluginClient($this->getMockedClient()),
            Psr17FactoryDiscovery::findRequestFactory(),
            Psr17FactoryDiscovery::findUrlFactory(),
            new ArrayHydrator()
        );

        $this->mockResponseFromPath(__DIR__.'/../../mocks/unitelegal/get/success.txt');

        $result = $api->get('849967211');

        /** @var RequestInterface $request */
        $request = $this->getLastRequest();

        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/siren/849967211', $request->getUri()->__toString());

        $this->assertTrue(is_array($result));
    }
}
