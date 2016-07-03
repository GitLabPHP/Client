<?php namespace Gitlab\HttpClient;

use Buzz\Client\ClientInterface;
use Buzz\Listener\ListenerInterface;
use Buzz\Message\Form\FormUpload;

use Gitlab\Exception\ErrorException;
use Gitlab\Exception\RuntimeException;
use Gitlab\HttpClient\Listener\ErrorListener;
use Gitlab\HttpClient\Message\Request;
use Gitlab\HttpClient\Message\Response;
use Gitlab\HttpClient\Message\FormRequest;

/**
 * Performs requests on Gitlab API. API documentation should be self-explanatory.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Matt Humphrey <matt@m4tt.co>
 */
class HttpClient implements HttpClientInterface
{
    /**
     * @var array
     */
    protected $options = array(
        'user_agent'  => 'php-gitlab-api (http://github.com/m4tthumphrey/php-gitlab-api)',
        'timeout'     => 10,
    );

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var ListenerInterface[]
     */
    protected $listeners = array();
    /**
     * @var array
     */
    protected $headers = array();

    /**
     * @var Response
     */
    private $lastResponse;

    /**
     * @var Request
     */
    private $lastRequest;

    /**
     * @param string $baseUrl
     * @param array $options
     * @param ClientInterface $client
     */
    public function __construct($baseUrl, array $options, ClientInterface $client)
    {
        $this->baseUrl = $baseUrl;
        $this->options = array_merge($this->options, $options);
        $this->client  = $client;

        $this->addListener(new ErrorListener($this->options));

        $this->clearHeaders();
    }

    /**
     * {@inheritDoc}
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    /**
     * Clears used headers
     */
    public function clearHeaders()
    {
        $this->headers = array();
    }

    /**
     * @param ListenerInterface $listener
     */
    public function addListener(ListenerInterface $listener)
    {
        $this->listeners[get_class($listener)] = $listener;
    }

    /**
     * {@inheritDoc}
     */
    public function get($path, array $parameters = array(), array $headers = array())
    {
        if (0 < count($parameters)) {
            $path .= (false === strpos($path, '?') ? '?' : '&').http_build_query($parameters, '', '&');
        }

        return $this->request($path, array(), 'GET', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function post($path, array $parameters = array(), array $headers = array(), array $files = array())
    {
        return $this->request($path, $parameters, 'POST', $headers, $files);
    }

    /**
     * {@inheritDoc}
     */
    public function patch($path, array $parameters = array(), array $headers = array())
    {
        return $this->request($path, $parameters, 'PATCH', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($path, array $parameters = array(), array $headers = array())
    {
        return $this->request($path, $parameters, 'DELETE', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function put($path, array $parameters = array(), array $headers = array())
    {
        return $this->request($path, $parameters, 'PUT', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function request($path, array $parameters = array(), $httpMethod = 'GET', array $headers = array(), array $files = array())
    {
        $path = trim($this->baseUrl.$path, '/');

        $request = $this->createRequest($httpMethod, $path, $parameters, $headers, $files);

        $hasListeners = 0 < count($this->listeners);
        if ($hasListeners) {
            foreach ($this->listeners as $listener) {
                $listener->preSend($request);
            }
        }

        $response = new Response();

        try {
            $this->client->send($request, $response);
        } catch (\LogicException $e) {
            throw new ErrorException($e->getMessage());
        } catch (\RuntimeException $e) {
            throw new RuntimeException($e->getMessage());
        }

        $this->lastRequest  = $request;
        $this->lastResponse = $response;

        if ($hasListeners) {
            foreach ($this->listeners as $listener) {
                $listener->postSend($request, $response);
            }
        }

        return $response;
    }

    /**
     * @return Request
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * @return Response
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * @param string $httpMethod
     * @param string $url
     * @param array $parameters
     * @param array $headers
     * @param array $files
     *
     * @return FormRequest|Request
     */
    private function createRequest($httpMethod, $url, array $parameters, array $headers, array $files)
    {
        if (empty($files)) {
            $request = new Request($httpMethod);
            $request->setContent(http_build_query($parameters));
        } else {
            $request = new FormRequest($httpMethod);
            foreach ($parameters as $name => $value) {
                $request->setField($name, $value);
            }

            foreach ($files as $name => $file) {
                $upload = new FormUpload($file);
                $request->setField($name, $upload);
            }
        }
        $request->setHeaders($this->headers);
        $request->fromUrl($url);
        $request->addHeaders($headers);

        return $request;
    }
}
