<?php namespace Gitlab\Api;

use Gitlab\Client;

/**
 * Abstract class for Api classes
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Matt Humphrey <matt@m4tt.co>
 */
abstract class AbstractApi
{
    /**
     * Default entries per page
     */
    const PER_PAGE = 20;

    /**
     * The client
     *
     * @var Client
     */
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return $this
     * @codeCoverageIgnore
     */
    public function configure()
    {
        return $this;
    }

    /**
     * @param string $path
     * @param array $parameters
     * @param array $requestHeaders
     * @return mixed
     */
    protected function get($path, array $parameters = array(), $requestHeaders = array())
    {
        $response = $this->client->getHttpClient()->get($path, $parameters, $requestHeaders);

        return $response->getContent();
    }

    /**
     * @param string $path
     * @param array $parameters
     * @param array $requestHeaders
     * @return mixed
     */
    protected function post($path, array $parameters = array(), $requestHeaders = array())
    {
        $response = $this->client->getHttpClient()->post($path, $parameters, $requestHeaders);

        return $response->getContent();
    }

    /**
     * @param string $path
     * @param array $parameters
     * @param array $requestHeaders
     * @return mixed
     */
    protected function patch($path, array $parameters = array(), $requestHeaders = array())
    {
        $response = $this->client->getHttpClient()->patch($path, $parameters, $requestHeaders);

        return $response->getContent();
    }

    /**
     * @param string $path
     * @param array $parameters
     * @param array $requestHeaders
     * @return mixed
     */
    protected function put($path, array $parameters = array(), $requestHeaders = array())
    {
        $response = $this->client->getHttpClient()->put($path, $parameters, $requestHeaders);

        return $response->getContent();
    }

    /**
     * @param string $path
     * @param array $parameters
     * @param array $requestHeaders
     * @return mixed
     */
    protected function delete($path, array $parameters = array(), $requestHeaders = array())
    {
        $response = $this->client->getHttpClient()->delete($path, $parameters, $requestHeaders);

        return $response->getContent();
    }

    /**
     * @param int $id
     * @param string $path
     * @return string
     */
    protected function getProjectPath($id, $path)
    {
        return 'projects/'.$this->encodePath($id).'/'.$path;
    }

    /**
     * @param string $path
     * @return string
     */
    protected function encodePath($path)
    {
        $path = rawurlencode($path);

        return str_replace('.', '%2E', $path);
    }
}
