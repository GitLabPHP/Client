<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Client;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Client\ClientInterface;

abstract class TestCase extends BaseTestCase
{
    /**
     * @return string
     */
    abstract protected function getApiClass();

    /**
     * @param array $methods
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getApiMock(array $methods = [])
    {
        $httpClient = $this->getMockBuilder(ClientInterface::class)
            ->setMethods(['sendRequest'])
            ->getMock();
        $httpClient
            ->expects($this->any())
            ->method('sendRequest');

        $client = Client::createWithHttpClient($httpClient);

        return $this->getMockBuilder($this->getApiClass())
            ->setMethods(array_merge(['getAsResponse', 'get', 'post', 'postRaw', 'patch', 'delete', 'put', 'head'], $methods))
            ->setConstructorArgs([$client, null])
            ->getMock();
    }
}
