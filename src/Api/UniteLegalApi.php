<?php

namespace Siren\Api;

class UniteLegalApi extends AbstractApi
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
        $response = $this->sendRequest('POST', '/siren', $query);

        return $this->hydrate($response);
    }

    /**
     * @param string $siren
     *
     * @return array|mixed
     *
     * @throws \Http\Client\Exception
     */
    public function get(string $siren)
    {
        $response = $this->sendRequest('GET', "/siren/$siren");

        return $this->hydrate($response);
    }
}
