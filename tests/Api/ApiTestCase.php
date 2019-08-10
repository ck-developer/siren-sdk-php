<?php

namespace Siren\Tests\Api;

use PHPUnit\Framework\TestCase;

abstract class ApiTestCase extends TestCase
{
    /**
     * @param $path
     *
     * @return array|\PHPUnit\Framework\MockObject\MockObject
     *
     * @throws \Exception
     */
    protected function mockResponse($path)
    {
        $response = array_map('trim', file($path));
        $headers = [];
        $body = trim(array_pop($response));

        list($protocol, $version) = explode('/', array_shift($response));
        list($version, $code) = explode(' ', trim($version));

        foreach ($response as $line) {
            $header = explode(': ', $line);
            if ($header[0] && $header[1]) {
                $headers[$header[0]][] = $header[1];
            }
        }

        $response = $this->getMockBuilder('Psr\Http\Message\ResponseInterface')->getMock();
        $stream = $this->getMockBuilder('Psr\Http\Message\StreamInterface')->getMock();

        $stream
            ->expects($this->any())
            ->method('__toString')
            ->willReturn($body)
        ;

        $stream
            ->expects($this->any())
            ->method('getContents')
            ->willReturn($body)
        ;

        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn($code)
        ;

        $response
            ->expects($this->any())
            ->method('getHeaders')
            ->willReturn($headers)
        ;

        $response
            ->expects($this->any())
            ->method('getBody')
            ->willReturn($stream)
        ;

        $response
            ->expects($this->any())
            ->method('getHeaderLine')
            ->willReturnMap(array_map(function ($header, $value) { return [$header, $value[0]]; }, array_keys($headers), $headers))
        ;

        return $response;
    }
}
