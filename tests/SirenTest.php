<?php

namespace Siren\Tests;

use Siren\Api;
use PHPUnit\Framework\TestCase;
use Siren\Siren;

class SirenTest extends TestCase
{
    /**
     * @var Siren
     */
    private $siren;

    public function setUp()
    {
        $this->siren = Siren::create('client_key', 'client_secret');
    }

    public function testUniteLegal()
    {
        $this->assertInstanceOf(Api\UniteLegalApi::class, $this->siren->unitLegal());
    }

    public function testEstablishment()
    {
        $this->assertInstanceOf(Api\EstablishmentApi::class, $this->siren->establishment());
    }
}
