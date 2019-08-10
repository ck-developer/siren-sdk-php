<?php

namespace Siren\Tests;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Mock\Client;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RequestInterface
     */
    protected $requestFactory;

    /**
     * @var Client
     */
    protected $mockedClient;

    /**
     * @param string $method
     * @param UriInterface|string $uri
     *
     * @return RequestInterface
     */
    protected function createRequest(string $method, $uri)
    {
        $this->requestFactory = $this->requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();

        return $this->requestFactory->createRequest($method, $uri);
    }

    protected function setUpMockedClient()
    {
        $this->mockedClient = $this->getMockedClient();
    }

    /**
     * @return Client
     */
    protected function getMockedClient()
    {
        return $this->mockedClient = $this->mockedClient ?? new Client();
    }

    /**
     * @return mixed|RequestInterface
     */
    public function getLastRequest()
    {
        return $this->mockedClient->getLastRequest();
    }

    protected function mockResponseFromArray(array $response)
    {
        $this->mockedClient->addResponse($this->createMockedResponseFromArray($response));
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|ResponseInterface
     */
    protected function createMockedResponseFromArray(array $data)
    {
        $response = $this->getMockBuilder('Psr\Http\Message\ResponseInterface')->getMock();
        $stream = $this->getMockBuilder('Psr\Http\Message\StreamInterface')->getMock();

        $stream
            ->expects($this->any())
            ->method('__toString')
            ->willReturn($data['body'] ?? '')
        ;

        $stream
            ->expects($this->any())
            ->method('getContents')
            ->willReturn($data['body'] ?? '')
        ;

        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn((int) $data['statusCode'])
        ;

        $response
            ->expects($this->any())
            ->method('getHeaders')
            ->willReturn($data['headers'])
        ;

        $response
            ->expects($this->any())
            ->method('getBody')
            ->willReturn($stream)
        ;

        $response
            ->expects($this->any())
            ->method('getHeaderLine')
            ->willReturnMap(array_map(function ($header, $value) { return [$header, $value[0]]; }, array_keys($data['headers']), $data['headers']))
        ;

        return $response;
    }

    protected function mockResponseFromPath(string $path)
    {
        $this->mockedClient->addResponse($this->createMockedResponseFromPath($path));
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|ResponseInterface
     */
    protected function createMockedResponseFromPath(string $path)
    {
        $response = [];
        $responseLines = array_map('trim', file($path));
        $response['body'] = trim(array_pop($responseLines));

        list($protocol, $parts) = explode('/', array_shift($responseLines));
        list($version, $statusCode) = explode(' ', trim($parts));

        $response['protocol'] = $protocol;
        $response['version'] = $version;
        $response['statusCode'] = $statusCode;

        foreach ($responseLines as $line) {
            if (\preg_match('/(.*): (.*)/', $line, $matches)) {
                list(, $name, $value) = $matches;

                $response['headers'][$name][] = $value;
            }
        }

        return $this->createMockedResponseFromArray($response);
    }

    public function addException(\Exception $exception)
    {
        $this->mockedClient->addException($exception);
    }
}
