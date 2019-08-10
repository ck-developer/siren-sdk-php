<?php

namespace Siren\Hydrator;

use Psr\Http\Message\ResponseInterface;

class ArrayHydrator implements HydratorInterface
{
    /**
     * @return array
     *
     * @throws \Exception
     */
    public function hydrate(ResponseInterface $response)
    {
        $body = $response->getBody()->__toString();

        if (0 !== strpos($response->getHeaderLine('Content-Type'), 'application/json')) {
            throw new \Exception('The ArrayHydrator cannot hydrate response with Content-Type:'.$response->getHeaderLine('Content-Type'));
        }

        $content = \json_decode($body, true);

        if (JSON_ERROR_NONE !== \json_last_error()) {
            throw new \Exception(sprintf('Error (%d) when trying to json_decode response', \json_last_error()));
        }

        return $content;
    }
}
