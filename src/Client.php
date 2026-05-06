<?php

declare(strict_types=1);

/*
 * This file is part of the Gitlab API library.
 *
 * (c) Matt Humphrey <matth@windsor-telecom.co.uk>
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitlab;

use Gitlab\Api\DeployKeys;
use Gitlab\Api\Deployments;
use Gitlab\Api\Environments;
use Gitlab\Api\Events;
use Gitlab\Api\Groups;
use Gitlab\Api\GroupsBoards;
use Gitlab\Api\GroupsEpics;
use Gitlab\Api\GroupsHooks;
use Gitlab\Api\GroupsMilestones;
use Gitlab\Api\Integrations;
use Gitlab\Api\IssueBoards;
use Gitlab\Api\IssueLinks;
use Gitlab\Api\Issues;
use Gitlab\Api\IssuesStatistics;
use Gitlab\Api\Jobs;
use Gitlab\Api\Keys;
use Gitlab\Api\MergeRequests;
use Gitlab\Api\Milestones;
use Gitlab\Api\PersonalAccessTokens;
use Gitlab\Api\ProjectNamespaces;
use Gitlab\Api\Projects;
use Gitlab\Api\Registry;
use Gitlab\Api\Repositories;
use Gitlab\Api\RepositoryFiles;
use Gitlab\Api\ResourceIterationEvents;
use Gitlab\Api\ResourceLabelEvents;
use Gitlab\Api\ResourceMilestoneEvents;
use Gitlab\Api\ResourceStateEvents;
use Gitlab\Api\ResourceWeightEvents;
use Gitlab\Api\Schedules;
use Gitlab\Api\Search;
use Gitlab\Api\Snippets;
use Gitlab\Api\SystemHooks;
use Gitlab\Api\Tags;
use Gitlab\Api\Users;
use Gitlab\Api\Version;
use Gitlab\Api\Wiki;
use Gitlab\HttpClient\Builder;
use Gitlab\HttpClient\Plugin\Authentication;
use Gitlab\HttpClient\Plugin\ExceptionThrower;
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
     * The job token authentication method.
     *
     * @var string
     */
    public const AUTH_HTTP_JOB_TOKEN = 'http_job_token';

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
    private const USER_AGENT = 'gitlab-php-api-client/12.1';

    private readonly Builder $httpClientBuilder;
    private readonly History $responseHistory;

    public function __construct(?Builder $httpClientBuilder = null)
    {
        $this->httpClientBuilder = $builder = $httpClientBuilder ?? new Builder();
        $this->responseHistory = new History();

        $builder->addPlugin(new ExceptionThrower());
        $builder->addPlugin(new HistoryPlugin($this->responseHistory));
        $builder->addPlugin(new HeaderDefaultsPlugin([
            'User-Agent' => self::USER_AGENT,
        ]));
        $builder->addPlugin(new RedirectPlugin());

        $this->setUrl(self::BASE_URL);
    }

    /**
     * Create a Gitlab\Client using an HTTP client.
     */
    public static function createWithHttpClient(ClientInterface $httpClient): self
    {
        $builder = new Builder($httpClient);

        return new self($builder);
    }

    public function deployKeys(): DeployKeys
    {
        return new DeployKeys($this);
    }

    public function deployments(): Deployments
    {
        return new Deployments($this);
    }

    public function environments(): Environments
    {
        return new Environments($this);
    }

    public function events(): Events
    {
        return new Events($this);
    }

    public function groups(): Groups
    {
        return new Groups($this);
    }

    public function groupsBoards(): GroupsBoards
    {
        return new GroupsBoards($this);
    }

    public function groupsEpics(): GroupsEpics
    {
        return new GroupsEpics($this);
    }

    public function groupsHooks(): GroupsHooks
    {
        return new GroupsHooks($this);
    }

    public function groupsMilestones(): GroupsMilestones
    {
        return new GroupsMilestones($this);
    }

    public function integrations(): Integrations
    {
        return new Integrations($this);
    }

    public function issueBoards(): IssueBoards
    {
        return new IssueBoards($this);
    }

    public function issueLinks(): IssueLinks
    {
        return new IssueLinks($this);
    }

    public function issues(): Issues
    {
        return new Issues($this);
    }

    public function resourceIterationEvents(): ResourceIterationEvents
    {
        return new ResourceIterationEvents($this);
    }

    public function resourceLabelEvents(): ResourceLabelEvents
    {
        return new ResourceLabelEvents($this);
    }

    public function resourceMilestoneEvents(): ResourceMilestoneEvents
    {
        return new ResourceMilestoneEvents($this);
    }

    public function resourceStateEvents(): ResourceStateEvents
    {
        return new ResourceStateEvents($this);
    }

    public function resourceWeightEvents(): ResourceWeightEvents
    {
        return new ResourceWeightEvents($this);
    }

    public function issuesStatistics(): IssuesStatistics
    {
        return new IssuesStatistics($this);
    }

    public function jobs(): Jobs
    {
        return new Jobs($this);
    }

    public function keys(): Keys
    {
        return new Keys($this);
    }

    public function mergeRequests(): MergeRequests
    {
        return new MergeRequests($this);
    }

    public function milestones(): Milestones
    {
        return new Milestones($this);
    }

    public function personalAccessTokens(): PersonalAccessTokens
    {
        return new PersonalAccessTokens($this);
    }

    public function namespaces(): ProjectNamespaces
    {
        return new ProjectNamespaces($this);
    }

    public function projects(): Projects
    {
        return new Projects($this);
    }

    public function registry(): Registry
    {
        return new Registry($this);
    }

    public function repositories(): Repositories
    {
        return new Repositories($this);
    }

    public function repositoryFiles(): RepositoryFiles
    {
        return new RepositoryFiles($this);
    }

    public function search(): Search
    {
        return new Search($this);
    }

    public function schedules(): Schedules
    {
        return new Schedules($this);
    }

    public function snippets(): Snippets
    {
        return new Snippets($this);
    }

    public function systemHooks(): SystemHooks
    {
        return new SystemHooks($this);
    }

    public function tags(): Tags
    {
        return new Tags($this);
    }

    public function users(): Users
    {
        return new Users($this);
    }

    public function version(): Version
    {
        return new Version($this);
    }

    public function wiki(): Wiki
    {
        return new Wiki($this);
    }

    /**
     * Authenticate a user for all next requests.
     *
     * @param string      $token      Gitlab private token
     * @param string      $authMethod One of the AUTH_* class constants
     */
    public function authenticate(#[\SensitiveParameter] string $token, string $authMethod, ?string $sudo = null): void
    {
        $this->getHttpClientBuilder()->removePlugin(Authentication::class);
        $this->getHttpClientBuilder()->addPlugin(new Authentication($authMethod, $token, $sudo));
    }

    public function setUrl(string $url): void
    {
        $uri = $this->getHttpClientBuilder()->getUriFactory()->createUri($url);

        $this->getHttpClientBuilder()->removePlugin(AddHostPlugin::class);
        $this->getHttpClientBuilder()->addPlugin(new AddHostPlugin($uri));
    }

    /**
     * Get the last response.
     */
    public function getLastResponse(): ?ResponseInterface
    {
        return $this->responseHistory->getLastResponse();
    }

    /**
     * Get the HTTP client.
     */
    public function getHttpClient(): HttpMethodsClientInterface
    {
        return $this->getHttpClientBuilder()->getHttpClient();
    }

    /**
     * Get the stream factory.
     */
    public function getStreamFactory(): StreamFactoryInterface
    {
        return $this->getHttpClientBuilder()->getStreamFactory();
    }

    /**
     * Get the HTTP client builder.
     */
    protected function getHttpClientBuilder(): Builder
    {
        return $this->httpClientBuilder;
    }
}
