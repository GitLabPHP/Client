<?php namespace Gitlab\Tests\Api;

use Gitlab\Client;
use Http\Client\HttpClient;

abstract class TestCase extends \PHPUnit_Framework_TestCase
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
            ->setMethods(array('sendRequest'))
            ->getMock();
        $httpClient
            ->expects($this->any())
            ->method('sendRequest');

        $client = Client::createWithHttpClient($httpClient);

        return $this->getMockBuilder($this->getApiClass())
            ->setMethods(array_merge(array('getAsResponse', 'get', 'post', 'postRaw', 'patch', 'delete', 'put', 'head'), $methods))
            ->setConstructorArgs(array($client))
            ->getMock();
    }
}
