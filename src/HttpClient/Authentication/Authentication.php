<?php

namespace Siren\HttpClient\Authentication;

use Http\Message\Authentication as AuthenticationInterface;
use Psr\Http\Message\RequestInterface;

class Authentication implements AuthenticationInterface
{
    /**
     * @var TokenProvider
     */
    private $provider;

    /**
     * Authentication constructor.
     */
    public function __construct(TokenProvider $provider)
    {
        $this->provider = $provider;
    }

    public function authenticate(RequestInterface $request)
    {
        $header = sprintf('Bearer %s', $this->provider->getAccessToken()->getToken());

        return $request->withHeader('Authorization', $header);
    }
}
