<?php

namespace Gitlab\HttpClient;
use Gitlab\HttpClient\Message\ResponseInterface;

/**
 * Performs requests on Gitlab API. API documentation should be self-explanatory.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
interface HttpClientInterface
{
    /**
     * Constant for authentication method. Indicates the default, but deprecated
     * login with username and token in URL.
     */
    const AUTH_URL_TOKEN = 'url_token';

    /**
     * Constant for authentication method. Indicates the new login method with
     * with username and token via HTTP Authentication.
     */
    const AUTH_HTTP_TOKEN = 'http_token';

    /**
     * Send a GET request
     *
     * @param string $path       Request path
     * @param array  $parameters GET Parameters
     * @param array  $headers    Reconfigure the request headers for this call only
     *
     * @return array ResponseInterface
     */
    public function get($path, array $parameters = array(), array $headers = array());

    /**
     * Send a POST request
     *
     * @param string $path       Request path
     * @param array  $parameters POST Parameters
     * @param array  $headers    Reconfigure the request headers for this call only
     *
     * @return array ResponseInterface
     */
    public function post($path, array $parameters = array(), array $headers = array());

    /**
     * Send a PATCH request
     *
     * @param string $path       Request path
     * @param array  $parameters PATCH Parameters
     * @param array  $headers    Reconfigure the request headers for this call only
     *
     * @return array ResponseInterface
     */
    public function patch($path, array $parameters = array(), array $headers = array());

    /**
     * Send a PUT request
     *
     * @param string $path       Request path
     * @param array  $parameters PUT Parameters
     * @param array  $headers    Reconfigure the request headers for this call only
     *
     * @return array ResponseInterface
     */
    public function put($path, array $parameters = array(), array $headers = array());

    /**
     * Send a DELETE request
     *
     * @param string $path       Request path
     * @param array  $parameters DELETE Parameters
     * @param array  $headers    Reconfigure the request headers for this call only
     *
     * @return array ResponseInterface
     */
    public function delete($path, array $parameters = array(), array $headers = array());

    /**
     * Send a request to the server, receive a response,
     * decode the response and returns an associative array
     *
     * @param string $path       Request API path
     * @param array  $parameters Parameters
     * @param string $httpMethod HTTP method to use
     * @param array  $headers    Request headers
     *
     * @return ResponseInterface
     */
    public function request($path, array $parameters = array(), $httpMethod = 'GET', array $headers = array());

    /**
     * Authenticate a user for all next requests
     *
     * @param string      $token      Gitlab private token
     * @param null|string $authMethod One of the AUTH_* class constants
     * @param null|string $sudo
     */
    public function authenticate($token, $authMethod = null, $sudo = null);

    /**
     * Set HTTP headers
     *
     * @param array $headers
     */
    public function setHeaders(array $headers);
}
