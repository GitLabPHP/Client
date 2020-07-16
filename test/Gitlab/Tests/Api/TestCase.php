<?php

namespace Gitlab\Tests\Api;

use Gitlab\Client;
use Http\Client\HttpClient;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @return string
     */
    abstract protected function getApiClass();

    /**
     * @param array $methods
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getApiMock(array $methods = [])
    {
        $httpClient = $this->getMockBuilder(HttpClient::class)
            ->setMethods(['sendRequest'])
            ->getMock();
        $httpClient
            ->expects($this->any())
            ->method('sendRequest');

        $client = Client::createWithHttpClient($httpClient);

        return $this->getMockBuilder($this->getApiClass())
            ->setMethods(\array_merge(['getAsResponse', 'get', 'post', 'postRaw', 'patch', 'delete', 'put', 'head'], $methods))
            ->setConstructorArgs([$client])
            ->getMock();
    }
}
