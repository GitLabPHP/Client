<?php

namespace Gitlab\Tests;

use Gitlab\Client;
use Http\Client\Common\HttpMethodsClient;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testCreateClient(): void
    {
        $client = new Client();

        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(HttpMethodsClient::class, $client->getHttpClient());
    }
}
