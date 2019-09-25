<?php namespace Gitlab;

use Gitlab\Api\AbstractApi;
use Gitlab\Exception\InvalidArgumentException;
use Gitlab\HttpClient\Builder;
use Gitlab\HttpClient\Plugin\ApiVersion;
use Gitlab\HttpClient\Plugin\History;
use Gitlab\HttpClient\Plugin\Authentication;
use Gitlab\HttpClient\Plugin\GitlabExceptionThrower;
use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\Plugin\HistoryPlugin;
use Http\Client\Common\Plugin\RedirectPlugin;
use Http\Client\HttpClient;
use Http\Discovery\UriFactoryDiscovery;

/**
 * Simple API wrapper for Gitlab
 *
 * @author Matt Humphrey <matt@m4tt.co>
 *
 * @property-read \Gitlab\Api\Groups $groups
 * @property-read \Gitlab\Api\Issues $issues
 * @property-read \Gitlab\Api\Jobs $jobs
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
 * @property-read \Gitlab\Api\Keys $keys
 * @property-read \Gitlab\Api\Tags $tags
 * @property-read \Gitlab\Api\Version $version
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
     * @var History
     */
    private $responseHistory;

    /**
     * @var Builder
     */
    private $httpClientBuilder;

    /**
     * Instantiate a new Gitlab client
     *
     * @param Builder $httpClientBuilder
     */
    public function __construct(Builder $httpClientBuilder = null)
    {
        $this->responseHistory = new History();
        $this->httpClientBuilder = $httpClientBuilder ?: new Builder();

        $this->httpClientBuilder->addPlugin(new GitlabExceptionThrower());
        $this->httpClientBuilder->addPlugin(new HistoryPlugin($this->responseHistory));
        $this->httpClientBuilder->addPlugin(new HeaderDefaultsPlugin([
            'User-Agent' => 'php-gitlab-api (http://github.com/m4tthumphrey/php-gitlab-api)',
        ]));
        $this->httpClientBuilder->addPlugin(new RedirectPlugin());
        $this->httpClientBuilder->addPlugin(new ApiVersion());

        $this->setUrl('https://gitlab.com');
    }

    /**
     * Create a Gitlab\Client using an url.
     *
     * @param string $url
     *
     * @return Client
     */
    public static function create($url)
    {
        $client = new self();
        $client->setUrl($url);

        return $client;
    }

    /**
     * Create a Gitlab\Client using an HttpClient.
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
     * @return Api\Groups
     */
    public function groups()
    {
        return new Api\Groups($this);
    }

    /**
     * @return Api\GroupsMilestones
     */
    public function groupsMilestones()
    {
        return new Api\GroupsMilestones($this);
    }

    /**
     * @return Api\Issues
     */
    public function issues()
    {
        return new Api\Issues($this);
    }

    /**
     * @return Api\IssueBoards
     */
    public function issueBoards()
    {
        return new Api\IssueBoards($this);
    }

    /**
     * @return Api\GroupsBoards
     */
    public function groupsBoards()
    {
        return new Api\GroupsBoards($this);
    }


    /**
     * @return Api\IssueLinks
     */
    public function issueLinks()
    {
        return new Api\IssueLinks($this);
    }

    /**
     * @return Api\Jobs
     */
    public function jobs()
    {
        return new Api\Jobs($this);
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
     * @return Api\Keys
     */
    public function keys()
    {
        return new Api\Keys($this);
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
     * @return Api\Schedules
     */
    public function schedules()
    {
        return new Api\Schedules($this);
    }

    /**
     * @return Api\IssuesStatistics
     */
    public function issuesStatistics()
    {
        return new Api\IssuesStatistics($this);
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

            case 'deploy_keys':
                return $this->deployKeys();

            case 'groups':
                return $this->groups();
                
            case 'groupsMilestones':
                return $this->groupsMilestones();

            case 'issues':
                return $this->issues();

            case 'board':
            case 'issue_boards':
                return $this->issueBoards();

            case 'group_boards':
                return $this->groupsBoards();

            case 'issue_links':
                return $this->issueLinks();

            case 'jobs':
                return $this->jobs();

            case 'mr':
            case 'merge_requests':
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
                
            case 'snippets':
                return $this->snippets();

            case 'hooks':
            case 'system_hooks':
                return $this->systemHooks();

            case 'users':
                return $this->users();

            case 'keys':
                return $this->keys();

            case 'tags':
                return $this->tags();

            case 'version':
                return $this->version();

            case 'environments':
                return $this->environments();

            case 'deployments':
                return $this->deployments();

            case 'schedules':
                return $this->schedules();

            case 'issues_statistics':
                return $this->issuesStatistics();


            default:
                throw new InvalidArgumentException('Invalid endpoint: "'.$name.'"');
        }
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
        $this->httpClientBuilder->removePlugin(Authentication::class);
        $this->httpClientBuilder->addPlugin(new Authentication($authMethod, $token, $sudo));

        return $this;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->httpClientBuilder->removePlugin(AddHostPlugin::class);
        $this->httpClientBuilder->addPlugin(new AddHostPlugin(UriFactoryDiscovery::find()->createUri($url)));

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

    /**
     * @return HttpMethodsClient
     */
    public function getHttpClient()
    {
        return $this->httpClientBuilder->getHttpClient();
    }

    /**
     * @return History
     */
    public function getResponseHistory()
    {
        return $this->responseHistory;
    }
}
