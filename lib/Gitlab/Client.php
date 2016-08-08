<?php namespace Gitlab;

use Buzz\Client\Curl;
use Buzz\Client\ClientInterface;

use Gitlab\Api\AbstractApi;
use Gitlab\Exception\InvalidArgumentException;
use Gitlab\HttpClient\HttpClient;
use Gitlab\HttpClient\HttpClientInterface;
use Gitlab\HttpClient\Listener\AuthListener;
use Gitlab\HttpClient\Listener\PaginationListener;

/**
 * Simple API wrapper for Gitlab
 *
 * @author Matt Humphrey <matt@m4tt.co>
 *
 * @property-read \Gitlab\Api\Groups $groups
 * @property-read \Gitlab\Api\Issues $issues
 * @property-read \Gitlab\Api\MergeRequests $merge_requests
 * @property-read \Gitlab\Api\MergeRequests $mr
 * @property-read \Gitlab\Api\Milestones $milestones
 * @property-read \Gitlab\Api\Milestones $ms
 * @property-read \Gitlab\Api\ProjectNamespaces $namespaces
 * @property-read \Gitlab\Api\ProjectNamespaces $ns
 * @property-read \Gitlab\Api\Projects $projects
 * @property-read \Gitlab\Api\Repositories $repositories
 * @property-read \Gitlab\Api\Repositories $repo
 * @property-read \Gitlab\Api\Snippets $snippets
 * @property-read \Gitlab\Api\SystemHooks $hooks
 * @property-read \Gitlab\Api\SystemHooks $system_hooks
 * @property-read \Gitlab\Api\Users $users
 */
class Client
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
     * Constant for authentication method. Indicates the OAuth method with a key
     * obtain using Gitlab's OAuth provider.
     */
    const AUTH_OAUTH_TOKEN = 'oauth_token';

    /**
     * @var array
     */
    private $options = array(
        'user_agent'  => 'php-gitlab-api (http://github.com/m4tthumphrey/php-gitlab-api)',
        'timeout'     => 60
    );

    private $baseUrl;

    /**
     * The Buzz instance used to communicate with Gitlab
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * Instantiate a new Gitlab client
     *
     * @param string               $baseUrl
     * @param null|ClientInterface $httpClient Buzz client
     */
    public function __construct($baseUrl, ClientInterface $httpClient = null)
    {
        $httpClient = $httpClient ?: new Curl();
        $httpClient->setTimeout($this->options['timeout']);
        $httpClient->setVerifyPeer(false);

        $this->baseUrl     = $baseUrl;
        $this->httpClient  = new HttpClient($this->baseUrl, $this->options, $httpClient);

        /**
         * a Pagination listener on Response
         */
        $this->httpClient->addListener(
            new PaginationListener()
        );
    }

    /**
     * @param string $name
     *
     * @return AbstractApi|mixed
     * @throws InvalidArgumentException
     */
    public function api($name)
    {
        switch ($name) {

            case 'groups':
                $api = new Api\Groups($this);
                break;

            case 'issues':
                $api = new Api\Issues($this);
                break;

            case 'mr':
            case 'merge_requests':
                $api = new Api\MergeRequests($this);
                break;

            case 'milestones':
            case 'ms':
                $api = new Api\Milestones($this);
                break;

            case 'namespaces':
            case 'ns':
                $api = new Api\ProjectNamespaces($this);
                break;

            case 'projects':
                $api = new Api\Projects($this);
                break;

            case 'repo':
            case 'repositories':
                $api = new Api\Repositories($this);
                break;

            case 'snippets':
                $api = new Api\Snippets($this);
                break;

            case 'hooks':
            case 'system_hooks':
                $api = new Api\SystemHooks($this);
                break;

            case 'users':
                $api = new Api\Users($this);
                break;

            default:
                throw new InvalidArgumentException('Invalid endpoint: "'.$name.'"');

        }

        return $api;
    }

    /**
     * Authenticate a user for all next requests
     *
     * @param string $token Gitlab private token
     * @param string $authMethod One of the AUTH_* class constants
     * @param string $sudo
     * @return $this
     */
    public function authenticate($token, $authMethod = self::AUTH_URL_TOKEN, $sudo = null)
    {
        $this->httpClient->addListener(
            new AuthListener(
                $authMethod,
                $token,
                $sudo
            )
        );

        return $this;
    }

    /**
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param HttpClientInterface $httpClient
     * @return $this
     */
    public function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Clears used headers
     *
     * @return $this
     */
    public function clearHeaders()
    {
        $this->httpClient->clearHeaders();

        return $this;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        $this->httpClient->setHeaders($headers);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function getOption($name)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new InvalidArgumentException(sprintf('Undefined option called: "%s"', $name));
        }

        return $this->options[$name];
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws InvalidArgumentException
     * @return $this
     */
    public function setOption($name, $value)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new InvalidArgumentException(sprintf('Undefined option called: "%s"', $name));
        }

        $this->options[$name] = $value;

        return $this;
    }

    /**
     * @param string $api
     * @return AbstractApi
     */
    public function __get($api)
    {
        return $this->api($api);
    }
}
