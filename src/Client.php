<?php

declare(strict_types=1);

namespace Gitlab;

use Gitlab\Api\DeployKeys;
use Gitlab\Api\Deployments;
use Gitlab\Api\Environments;
use Gitlab\Api\Groups;
use Gitlab\Api\GroupsBoards;
use Gitlab\Api\GroupsMilestones;
use Gitlab\Api\IssueBoards;
use Gitlab\Api\IssueLinks;
use Gitlab\Api\Issues;
use Gitlab\Api\IssuesStatistics;
use Gitlab\Api\Jobs;
use Gitlab\Api\Keys;
use Gitlab\Api\MergeRequests;
use Gitlab\Api\Milestones;
use Gitlab\Api\ProjectNamespaces;
use Gitlab\Api\Projects;
use Gitlab\Api\Repositories;
use Gitlab\Api\RepositoryFiles;
use Gitlab\Api\Schedules;
use Gitlab\Api\Snippets;
use Gitlab\Api\SystemHooks;
use Gitlab\Api\Tags;
use Gitlab\Api\Users;
use Gitlab\Api\Version;
use Gitlab\Api\Wiki;
use Gitlab\HttpClient\Builder;
use Gitlab\HttpClient\Plugin\Authentication;
use Gitlab\HttpClient\Plugin\GitlabExceptionThrower;
use Gitlab\HttpClient\Plugin\History;
use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\Plugin\HistoryPlugin;
use Http\Client\Common\Plugin\RedirectPlugin;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

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
     * The default base URL.
     *
     * @var string
     */
    private const BASE_URL = 'https://gitlab.com';

    /**
     * The default user agent header.
     *
     * @var string
     */
    private const USER_AGENT = 'gitlab-php-api-client/10.4';

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
            'User-Agent' => self::USER_AGENT,
        ]));
        $builder->addPlugin(new RedirectPlugin());

        $this->setUrl(self::BASE_URL);
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
     * @return DeployKeys
     */
    public function deployKeys()
    {
        return new DeployKeys($this);
    }

    /**
     * @return Deployments
     */
    public function deployments()
    {
        return new Deployments($this);
    }

    /**
     * @return Environments
     */
    public function environments()
    {
        return new Environments($this);
    }

    /**
     * @return Groups
     */
    public function groups()
    {
        return new Groups($this);
    }

    /**
     * @return GroupsBoards
     */
    public function groupsBoards()
    {
        return new GroupsBoards($this);
    }

    /**
     * @return GroupsMilestones
     */
    public function groupsMilestones()
    {
        return new GroupsMilestones($this);
    }

    /**
     * @return IssueBoards
     */
    public function issueBoards()
    {
        return new IssueBoards($this);
    }

    /**
     * @return IssueLinks
     */
    public function issueLinks()
    {
        return new IssueLinks($this);
    }

    /**
     * @return Issues
     */
    public function issues()
    {
        return new Issues($this);
    }

    /**
     * @return IssuesStatistics
     */
    public function issuesStatistics()
    {
        return new IssuesStatistics($this);
    }

    /**
     * @return Jobs
     */
    public function jobs()
    {
        return new Jobs($this);
    }

    /**
     * @return Keys
     */
    public function keys()
    {
        return new Keys($this);
    }

    /**
     * @return MergeRequests
     */
    public function mergeRequests()
    {
        return new MergeRequests($this);
    }

    /**
     * @return Milestones
     */
    public function milestones()
    {
        return new Milestones($this);
    }

    /**
     * @return ProjectNamespaces
     */
    public function namespaces()
    {
        return new ProjectNamespaces($this);
    }

    /**
     * @return Projects
     */
    public function projects()
    {
        return new Projects($this);
    }

    /**
     * @return Repositories
     */
    public function repositories()
    {
        return new Repositories($this);
    }

    /**
     * @return RepositoryFiles
     */
    public function repositoryFiles()
    {
        return new RepositoryFiles($this);
    }

    /**
     * @return Schedules
     */
    public function schedules()
    {
        return new Schedules($this);
    }

    /**
     * @return Snippets
     */
    public function snippets()
    {
        return new Snippets($this);
    }

    /**
     * @return SystemHooks
     */
    public function systemHooks()
    {
        return new SystemHooks($this);
    }

    /**
     * @return Tags
     */
    public function tags()
    {
        return new Tags($this);
    }

    /**
     * @return Users
     */
    public function users()
    {
        return new Users($this);
    }

    /**
     * @return Version
     */
    public function version()
    {
        return new Version($this);
    }

    /**
     * @return Wiki
     */
    public function wiki()
    {
        return new Wiki($this);
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
        $uri = $this->getHttpClientBuilder()->getUriFactory()->createUri($url);

        $this->getHttpClientBuilder()->removePlugin(AddHostPlugin::class);
        $this->getHttpClientBuilder()->addPlugin(new AddHostPlugin($uri));

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
     * Get the stream factory.
     *
     * @return StreamFactoryInterface
     */
    public function getStreamFactory()
    {
        return $this->getHttpClientBuilder()->getStreamFactory();
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
