<?php

namespace Gitlab;

use Gitlab\HttpClient\Builder;
use Gitlab\HttpClient\Plugin\ApiVersion;
use Gitlab\HttpClient\Plugin\Authentication;
use Gitlab\HttpClient\Plugin\GitlabExceptionThrower;
use Gitlab\HttpClient\Plugin\History;
use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\Plugin\HistoryPlugin;
use Http\Client\Common\Plugin\RedirectPlugin;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Simple API wrapper for Gitlab.
 *
 * @author Matt Humphrey <matt@m4tt.co>
 */
class Client
{
    /**
     * The private token authentication method.
     *
     * @var string
     */
    public const AUTH_HTTP_TOKEN = 'http_token';

    /**
     * The OAuth 2 token authentication method.
     *
     * @var string
     */
    public const AUTH_OAUTH_TOKEN = 'oauth_token';

    /**
     * The HTTP client builder.
     *
     * @var Builder
     */
    private $httpClientBuilder;

    /**
     * The response history plugin.
     *
     * @var History
     */
    private $responseHistory;

    /**
     * Instantiate a new Gitlab client.
     *
     * @param Builder|null $httpClientBuilder
     *
     * @return void
     */
    public function __construct(Builder $httpClientBuilder = null)
    {
        $this->httpClientBuilder = $builder = $httpClientBuilder ?? new Builder();
        $this->responseHistory = new History();

        $builder->addPlugin(new GitlabExceptionThrower());
        $builder->addPlugin(new HistoryPlugin($this->responseHistory));
        $builder->addPlugin(new HeaderDefaultsPlugin([
            'User-Agent' => 'php-gitlab-api (http://github.com/m4tthumphrey/php-gitlab-api)',
        ]));
        $builder->addPlugin(new RedirectPlugin());
        $builder->addPlugin(new ApiVersion());

        $this->setUrl('https://gitlab.com');
    }

    /**
     * Create a Gitlab\Client using an HTTP client.
     *
     * @param ClientInterface $httpClient
     *
     * @return Client
     */
    public static function createWithHttpClient(ClientInterface $httpClient)
    {
        $builder = new Builder($httpClient);

        return new self($builder);
    }

    /**
     * @return Api\DeployKeys
     */
    public function deployKeys()
    {
        return new Api\DeployKeys($this);
    }

    /**
     * @return Api\Deployments
     */
    public function deployments()
    {
        return new Api\Deployments($this);
    }

    /**
     * @return Api\Environments
     */
    public function environments()
    {
        return new Api\Environments($this);
    }

    /**
     * @return Api\Groups
     */
    public function groups()
    {
        return new Api\Groups($this);
    }

    /**
     * @return Api\GroupsBoards
     */
    public function groupsBoards()
    {
        return new Api\GroupsBoards($this);
    }

    /**
     * @return Api\GroupsMilestones
     */
    public function groupsMilestones()
    {
        return new Api\GroupsMilestones($this);
    }

    /**
     * @return Api\IssueBoards
     */
    public function issueBoards()
    {
        return new Api\IssueBoards($this);
    }

    /**
     * @return Api\IssueLinks
     */
    public function issueLinks()
    {
        return new Api\IssueLinks($this);
    }

    /**
     * @return Api\Issues
     */
    public function issues()
    {
        return new Api\Issues($this);
    }

    /**
     * @return Api\IssuesStatistics
     */
    public function issuesStatistics()
    {
        return new Api\IssuesStatistics($this);
    }

    /**
     * @return Api\Jobs
     */
    public function jobs()
    {
        return new Api\Jobs($this);
    }

    /**
     * @return Api\Keys
     */
    public function keys()
    {
        return new Api\Keys($this);
    }

    /**
     * @return Api\MergeRequests
     */
    public function mergeRequests()
    {
        return new Api\MergeRequests($this);
    }

    /**
     * @return Api\Milestones
     */
    public function milestones()
    {
        return new Api\Milestones($this);
    }

    /**
     * @return Api\ProjectNamespaces
     */
    public function namespaces()
    {
        return new Api\ProjectNamespaces($this);
    }

    /**
     * @return Api\Projects
     */
    public function projects()
    {
        return new Api\Projects($this);
    }

    /**
     * @return Api\Repositories
     */
    public function repositories()
    {
        return new Api\Repositories($this);
    }

    /**
     * @return Api\RepositoryFiles
     */
    public function repositoryFiles()
    {
        return new Api\RepositoryFiles($this);
    }

    /**
     * @return Api\Schedules
     */
    public function schedules()
    {
        return new Api\Schedules($this);
    }

    /**
     * @return Api\Snippets
     */
    public function snippets()
    {
        return new Api\Snippets($this);
    }

    /**
     * @return Api\SystemHooks
     */
    public function systemHooks()
    {
        return new Api\SystemHooks($this);
    }

    /**
     * @return Api\Users
     */
    public function users()
    {
        return new Api\Users($this);
    }

    /**
     * @return Api\Tags
     */
    public function tags()
    {
        return new Api\Tags($this);
    }

    /**
     * @return Api\Version
     */
    public function version()
    {
        return new Api\Version($this);
    }

    /**
     * @return Api\Wiki
     */
    public function wiki()
    {
        return new Api\Wiki($this);
    }

    /**
     * Authenticate a user for all next requests.
     *
     * @param string      $token      Gitlab private token
     * @param string      $authMethod One of the AUTH_* class constants
     * @param string|null $sudo
     *
     * @return $this
     */
    public function authenticate(string $token, string $authMethod, string $sudo = null)
    {
        $this->getHttpClientBuilder()->removePlugin(Authentication::class);
        $this->getHttpClientBuilder()->addPlugin(new Authentication($authMethod, $token, $sudo));

        return $this;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl(string $url)
    {
        $this->getHttpClientBuilder()->removePlugin(AddHostPlugin::class);
        $this->getHttpClientBuilder()->addPlugin(new AddHostPlugin(Psr17FactoryDiscovery::findUrlFactory()->createUri($url)));

        return $this;
    }

    /**
     * Get the last response.
     *
     * @return ResponseInterface|null
     */
    public function getLastResponse()
    {
        return $this->responseHistory->getLastResponse();
    }

    /**
     * Get the HTTP client.
     *
     * @return HttpMethodsClientInterface
     */
    public function getHttpClient()
    {
        return $this->getHttpClientBuilder()->getHttpClient();
    }

    /**
     * Get the HTTP client builder.
     *
     * @return Builder
     */
    protected function getHttpClientBuilder()
    {
        return $this->httpClientBuilder;
    }
}
