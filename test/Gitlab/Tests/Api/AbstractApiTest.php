<?php namespace Gitlab\Tests\Api;

use Gitlab\Client;
use Http\Client\HttpClient;
use ReflectionClass;

class AbstractApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldPrepareBodyWithCleanArrays()
    {
        $parameters = [
            'array_param' => [
                'value1',
                'value2'
            ]
        ];
        $expectedBody = 'array_param[]=value1&array_param[]=value2';

        $abstractApiMock = $this->getAbstractApiMock();
        $reflection = new ReflectionClass(get_class($abstractApiMock));
        $method = $reflection->getMethod('prepareBody');
        $method->setAccessible(true);
        $stream = $method->invokeArgs(
            $abstractApiMock,
            [
                $parameters
            ]
        );

        $this->assertEquals($expectedBody, urldecode((string)$stream));
    }

    protected function getAbstractApiMock(array $methods = [])
    {
        $httpClient = $this->getMockBuilder(HttpClient::class)
            ->setMethods(array('sendRequest'))
            ->getMock()
        ;
        $httpClient
            ->expects($this->any())
            ->method('sendRequest')
        ;
        $client = Client::createWithHttpClient($httpClient);

        $abstractApiMock = $this->getMockBuilder('Gitlab\Api\AbstractApi')
            ->setConstructorArgs([
                $client,
                null
            ])
            ->setMethods($methods)
            ->getMockForAbstractClass()
        ;

        return $abstractApiMock;
    }
}
