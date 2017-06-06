<?php namespace Gitlab\Tests;

use Buzz\Client\Curl;

use Gitlab\Client;
use Gitlab\HttpClient\HttpClientInterface;


abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Client
     */
    protected function getClientMock()
    {
        return new Client($this->getHttpMock());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|HttpClientInterface
     */
    protected function getHttpMock()
    {
        return $this->getMock('Gitlab\HttpClient\HttpClient', array(), array(null, array(), $this->getHttpClientMock()));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Curl
     */
    protected function getHttpClientMock()
    {
        $httpClient = $this->getMock('Buzz\Client\Curl', array('send'));
        $httpClient
            ->expects($this->any())
            ->method('send');

        return $httpClient;
    }
}
