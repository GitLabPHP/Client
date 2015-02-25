<?php namespace Gitlab\Tests\Api;

use Gitlab\Client;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    abstract protected function getApiClass();

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|mixed
     */
    protected function getApiMock($methods = array())
    {
        $httpClient = $this->getMock('Buzz\Client\Curl', array('send'));
        $httpClient
            ->expects($this->any())
            ->method('send');

        $mock = $this->getMock('Gitlab\HttpClient\HttpClient', array(), array(null, array(), $httpClient));

        $client = new Client($mock);
        $client->setHttpClient($mock);

        $methods = array_merge(array('get', 'post', 'postRaw', 'patch', 'delete', 'put', 'head'), $methods);

        return $this->getMockBuilder($this->getApiClass())
            ->setMethods($methods)
            ->setConstructorArgs(array($client))
            ->getMock()
        ;
    }
}
