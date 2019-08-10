<?php

namespace Siren\Api;

class EstablishmentApi extends AbstractApi
{
    /**
     * @param array $query
     *
     * @return array|mixed
     *
     * @throws \Http\Client\Exception
     */
    public function search(array $query)
    {
        $response = $this->sendRequest('POST', '/siret', $query);

        return $this->hydrate($response);
    }

    /**
     * @param string $siret
     *
     * @return array|mixed
     *
     * @throws \Http\Client\Exception
     */
    public function get(string $siret)
    {
        $response = $this->sendRequest('GET', "/siret/$siret");

        return $this->hydrate($response);
    }
}
