<?php

namespace Gitlab;

use Gitlab\Api\AbstractApi;
use Gitlab\Exception\InvalidArgumentException;
use Gitlab\HttpClient\Builder;
use Gitlab\HttpClient\Plugin\ApiVersion;
use Gitlab\HttpClient\Plugin\Authentication;
use Gitlab\HttpClient\Plugin\GitlabExceptionThrower;
use Gitlab\HttpClient\Plugin\History;
use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\Plugin\HistoryPlugin;
use Http\Client\Common\Plugin\RedirectPlugin;
use Http\Client\HttpClient;
use Http\Message\StreamFactory;
use Psr\Http\Message\ResponseInterface;

/**
 * Simple API wrapper for Gitlab.
 *
 * @author Matt Humphrey <matt@m4tt.co>
 *
 * @property-read \Gitlab\Api\DeployKeys $deploy_keys @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\Deployments $deployments @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\Environments $environments @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\Groups $groups @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\GroupsBoards $groups_boards @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\GroupsMilestones $groups_milestones @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\IssueBoards $board @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\IssueBoards $issue_boards @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\IssueLinks $issue_links @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\Issues $issues @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\IssuesStatistics $issues_statistics @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\Jobs $jobs @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\Keys $keys @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\MergeRequests $merge_requests @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\MergeRequests $mr @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\Milestones $milestones @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\Milestones $ms @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\ProjectNamespaces $namespaces @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\ProjectNamespaces $ns @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\Projects $projects @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\Repositories $repo @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\Repositories $repositories @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\RepositoryFiles $repositoryFiles @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\Schedules $schedules @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\Snippets $snippets @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\SystemHooks $hooks @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\SystemHooks $system_hooks @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\Users $users @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\Tags $tags @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\Version $version @deprecated since version 9.18 and will be removed in 10.0.
 * @property-read \Gitlab\Api\Wiki $wiki @deprecated since version 9.18 and will be removed in 10.0.
 */
class Client
{
    /**
     * The URL token authentication method.
     *
     * @var string
     *
     * @deprecated since version 9.18 and will be removed in 10.0.
     */
    const AUTH_URL_TOKEN = 'url_token';

    /**
     * The private token authentication method.
     *
     * @var string
     */
    const AUTH_HTTP_TOKEN = 'http_token';

    /**
     * The OAuth 2 token authentication method.
     *
     * @var string
     */
    const AUTH_OAUTH_TOKEN = 'oauth_token';

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
        $this->httpClientBuilder = $builder = null === $httpClientBuilder ? new Builder() : $httpClientBuilder;
        $this->responseHistory = new History();

        $builder->addPlugin(new GitlabExceptionThrower());
        $builder->addPlugin(new HistoryPlugin($this->responseHistory));
        $builder->addPlugin(new HeaderDefaultsPlugin([
            'User-Agent' => 'php-gitlab-api (https://github.com/GitLabPHP/Client)',
        ]));
        $builder->addPlugin(new RedirectPlugin());
        $builder->addPlugin(new ApiVersion());

        $this->setUrl('https://gitlab.com');
    }

    /**
     * Create a Gitlab\Client using an url.
     *
     * @param string $url
     *
     * @return Client
     *
     * @deprecated since version 9.18 and will be removed in 10.0. Use the setUrl() method after instantiation instead.
     */
    public static function create($url)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.18 and will be removed in 10.0. Use the setUrl() method after instantiation instead.', __METHOD__), E_USER_DEPRECATED);

        $client = new self();
        $client->setUrl($url);

        return $client;
    }

    /**
     * Create a Gitlab\Client using an HTTP client.
     *
     * @param HttpClient $httpClient
     *
     * @return Client
     */
    public static function createWithHttpClient(HttpClient $httpClient)
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
     * @param string $name
     *
     * @return AbstractApi|mixed
     *
     * @throws InvalidArgumentException
     *
     * @deprecated since version 9.18 and will be removed in 10.0. Use the direct methods instead.
     */
    public function api($name)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.18 and will be removed in 10.0. Use the direct methods instead.', __METHOD__), E_USER_DEPRECATED);

        switch ($name) {
            case 'deploy_keys':
                return $this->deployKeys();

            case 'deployments':
                return $this->deployments();

            case 'environments':
                return $this->environments();

            case 'groups':
                return $this->groups();

            case 'groups_boards':
                return $this->groupsBoards();

            case 'groups_milestones':
                return $this->groupsMilestones();

            case 'board':
            case 'issue_boards':
                return $this->issueBoards();

            case 'issue_links':
                return $this->issueLinks();

            case 'issues':
                return $this->issues();

            case 'issues_statistics':
                return $this->issuesStatistics();

            case 'jobs':
                return $this->jobs();

            case 'keys':
                return $this->keys();

            case 'merge_requests':
            case 'mr':
                return $this->mergeRequests();

            case 'milestones':
            case 'ms':
                return $this->milestones();

            case 'namespaces':
            case 'ns':
                return $this->namespaces();

            case 'projects':
                return $this->projects();

            case 'repo':
            case 'repositories':
                return $this->repositories();

            case 'repositoryFiles':
                return $this->repositoryFiles();

            case 'schedules':
                return $this->schedules();

            case 'snippets':
                return $this->snippets();

            case 'hooks':
            case 'system_hooks':
                return $this->systemHooks();

            case 'users':
                return $this->users();

            case 'tags':
                return $this->tags();

            case 'version':
                return $this->version();

            case 'wiki':
                return $this->wiki();

            default:
                throw new InvalidArgumentException('Invalid endpoint: "'.$name.'"');
        }
    }

    /**
     * Authenticate a user for all next requests.
     *
     * @param string      $token      Gitlab private token
     * @param string|null $authMethod One of the AUTH_* class constants
     * @param string|null $sudo
     *
     * @return $this
     */
    public function authenticate($token, $authMethod = null, $sudo = null)
    {
        if (null === $authMethod) {
            @trigger_error(sprintf('The $authMethod will become required in version 10.0. Not providing an explicit authentication method is deprecated since version 9.18.'), E_USER_DEPRECATED);
            $authMethod = self::AUTH_URL_TOKEN;
        } elseif (self::AUTH_URL_TOKEN === $authMethod) {
            @trigger_error(sprintf('The AUTH_URL_TOKEN authentivation method is deprecated since version 9.18 and will be removed in 10.0. Use AUTH_HTTP_TOKEN instead.'), E_USER_DEPRECATED);
        } elseif (self::AUTH_HTTP_TOKEN !== $authMethod && self::AUTH_OAUTH_TOKEN !== $authMethod) {
            @trigger_error(sprintf('Passing an invalid authentication method is deprecated since version 9.1 and will be banned in version 10.0.'), E_USER_DEPRECATED);
        }

        $this->getHttpClientBuilder()->removePlugin(Authentication::class);
        $this->getHttpClientBuilder()->addPlugin(new Authentication($authMethod, $token, $sudo));

        return $this;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $uri = $this->getHttpClientBuilder()->getUriFactory()->createUri($url);

        $this->getHttpClientBuilder()->removePlugin(AddHostPlugin::class);
        $this->getHttpClientBuilder()->addPlugin(new AddHostPlugin($uri));

        return $this;
    }

    /**
     * @param string $api
     *
     * @return AbstractApi
     *
     * @deprecated since version 9.18 and will be removed in 10.0. Use the direct methods instead.
     */
    public function __get($api)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.18 and will be removed in 10.0. Use the direct methods instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->api($api);
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
     * @return History
     *
     * @deprecated since version 9.18 and will be removed in 10.0. Use the getLastResponse() method instead.
     */
    public function getResponseHistory()
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.18 and will be removed in 10.0. Use the getLastResponse() method instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->responseHistory;
    }

    /**
     * Get the HTTP client.
     *
     * @return HttpMethodsClient
     */
    public function getHttpClient()
    {
        return $this->getHttpClientBuilder()->getHttpClient();
    }

    /**
     * Get the stream factory.
     *
     * @return StreamFactory
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
