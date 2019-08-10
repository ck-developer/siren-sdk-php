<?php

namespace Siren\Hydrator;

use Psr\Http\Message\ResponseInterface;

interface HydratorInterface
{
    public function hydrate(ResponseInterface $response);
}
