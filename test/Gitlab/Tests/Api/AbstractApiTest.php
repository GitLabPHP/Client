<?php namespace Gitlab\Tests;

use Gitlab\Api\AbstractApi;
use Gitlab\Client;
use Gitlab\Tests\TestCase;
use Gitlab\HttpClient\Message\Response;

class AbstractApiTest extends TestCase
{
    /**
     * @test
     */
    public function shouldPassGETRequestToClient()
    {
        $response = $this->getResponse('value');

        $httpClient = $this->getHttpMock();
        $httpClient
            ->expects($this->any())
            ->method('get')
            ->with('/path', array('param1' => 'param1value'), array('header1' => 'header1value'))
            ->will($this->returnValue($response));

        $client = $this->getClientMock();
        $client->setHttpClient($httpClient);

        $api = $this->getAbstractApiObject($client);
        $this->assertEquals('value', $api->get('/path', array('param1' => 'param1value'), array('header1' => 'header1value')));
    }

    /**
     * @test
     */
    public function shouldPassPOSTRequestToClient()
    {
        $response = $this->getResponse('value');

        $httpClient = $this->getHttpMock();
        $httpClient
            ->expects($this->any())
            ->method('post')
            ->with('/path', array('param1' => 'param1value'), array('header1' => 'header1value'))
            ->will($this->returnValue($response));

        $client = $this->getClientMock();
        $client->setHttpClient($httpClient);

        $api = $this->getAbstractApiObject($client);
        $this->assertEquals('value', $api->post('/path', array('param1' => 'param1value'), array('header1' => 'header1value')));
    }

    /**
     * @test
     */
    public function shouldPassPUTRequestToClient()
    {
        $response = $this->getResponse('value');

        $httpClient = $this->getHttpMock();
        $httpClient
            ->expects($this->any())
            ->method('put')
            ->with('/path', array('param1' => 'param1value'), array('header1' => 'header1value'))
            ->will($this->returnValue($response));

        $client = $this->getClientMock();
        $client->setHttpClient($httpClient);

        $api = $this->getAbstractApiObject($client);
        $this->assertEquals('value', $api->put('/path', array('param1' => 'param1value'), array('header1' => 'header1value')));
    }

    /**
     * @test
     */
    public function shouldPassDELETERequestToClient()
    {
        $response = $this->getResponse('value');

        $httpClient = $this->getHttpMock();
        $httpClient
            ->expects($this->any())
            ->method('delete')
            ->with('/path', array('param1' => 'param1value'), array('header1' => 'header1value'))
            ->will($this->returnValue($response));

        $client = $this->getClientMock();
        $client->setHttpClient($httpClient);

        $api = $this->getAbstractApiObject($client);
        $this->assertEquals('value', $api->delete('/path', array('param1' => 'param1value'), array('header1' => 'header1value')));
    }

    /**
     * @test
     */
    public function shouldPassPATCHRequestToClient()
    {
        $response = $this->getResponse('value');

        $httpClient = $this->getHttpMock();
        $httpClient
            ->expects($this->any())
            ->method('patch')
            ->with('/path', array('param1' => 'param1value'), array('header1' => 'header1value'))
            ->will($this->returnValue($response));

        $client = $this->getClientMock();
        $client->setHttpClient($httpClient);

        $api = $this->getAbstractApiObject($client);
        $this->assertEquals('value', $api->patch('/path', array('param1' => 'param1value'), array('header1' => 'header1value')));
    }

    /**
     * @param mixed $value
     * @return Response
     */
    protected function getResponse($value)
    {
        $response = new Response();
        $response->setContent($value);

        return $response;
    }

    /**
     * @param Client $client
     * @return AbstractApiTestInstance
     */
    protected function getAbstractApiObject(Client $client)
    {
        return new AbstractApiTestInstance($client);
    }
}

class AbstractApiTestInstance extends AbstractApi
{
    /**
     * {@inheritDoc}
     */
    public function get($path, array $parameters = array(), $requestHeaders = array())
    {
        return parent::get($path, $parameters, $requestHeaders);
    }

    /**
     * {@inheritDoc}
     */
    public function post($path, array $parameters = array(), $requestHeaders = array(), array $files = array())
    {
        return parent::post($path, $parameters, $requestHeaders, $files);
    }

    /**
     * {@inheritDoc}
     */
    public function patch($path, array $parameters = array(), $requestHeaders = array())
    {
        return parent::patch($path, $parameters, $requestHeaders);
    }

    /**
     * {@inheritDoc}
     */
    public function put($path, array $parameters = array(), $requestHeaders = array())
    {
        return parent::put($path, $parameters, $requestHeaders);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($path, array $parameters = array(), $requestHeaders = array())
    {
        return parent::delete($path, $parameters, $requestHeaders);
    }
}
