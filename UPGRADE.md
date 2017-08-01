# UPGRADE FROM 8.0 to 9.0

Since 9.0, lib no longer use buzz 0.7+, instead it has an HTTPlug abstraction layer.

## `Gitlab\Client` changes

* The constructor no longer allow to specify base url. Use `setUrl` or `Client::create` instead.
* The default url is set to `https://gitlab.com`.
* The `$options` constructor argument have been removed, the `getOption` and `setOption` methods have been removed.
See [documentation](doc/customize.md) to know how to customize the client timeout and how to use a custom user agent.
* The `setBaseUrl` and `getBaseUrl` methods have been removed. Use `setUrl` instead.
* The `clearHeaders` and `setHeaders` methods have been removed. See [documentation](doc/customize.md) to know how use custom headers.
* The `setHttpClient` method have been removed. Use a `Gitlab\HttpClient\Builder` instead. 
* The `getHttpClient` method return type is changed to `Http\Client\Common\HttpMethodsClient`.

## `Gitlab\Api\DeployKeys` changes

* The `all` method now take a single argument which is an associative array of query string parameters.
* The `ORDER_BY` and `SORT` class constants have been removed.

## `Gitlab\Api\Groups` changes

* The `visibility_level` parameter have been removed from `create` method. Use `visibility` instead.
* The `all` method now take a single argument which is an associative array of query string parameters.
* The `search` method have been removed. Use `all` method instead.
* The `members` method second and subsequent arguments have been replaced by a single associative array of query string parameters.
* The `projects` method second and subsequent arguments have been replaced by a single associative array of query string parameters.

## `Gitlab\Api\Issues` changes

* The second argument of `update`, `remove`, `showComments`, `showComment`, `addComment`, `updateComment`, `removeComment`,
 `setTimeEstimate`, `resetTimeEstimate`, `addSpentTime` and `resetSpentTime` methods is now a scoped issue id (iid).
* The `all` method second and subsequent arguments have been replaced by a single associative array of query string parameters.

## `Gitlab\Api\IssueBoards` changes

* The `all` method second and subsequent arguments have been replaced by a single associative array of query string parameters.

## `Gitlab\Api\MergeRequests` changes

* The `getList`, `getByIid`, `merged`, `opened` and `closed` methods have been removed. Use `all` method instead.
* The `ORDER_BY` and `SORT` class constants have been removed.
* The `all` method now take a single argument which is an associative array of query string parameters.
* The `getNotes` method now take only two arguments, the project id and the merge request iid.

## `Gitlab\Api\Milestones` changes

* The `all` method second and subsequent arguments have been replaced by a single associative array of query string parameters.

## `Gitlab\Api\Projects` changes

* The `keys`, `key`, `addKey`, `removeKey`, `disableKey` and `enableKey` methods have been removed.
Use the `deployKeys`, `deployKey`, `addDeployKey`, `deleteDeployKey`, `removeDeployKey` and `enableDeployKey` instead.
* The `ORDER_BY` and `SORT` class constants have been removed.
* The `accessible`, `owned` and `search` methods have been removed. Use `all` method instead.
* The `all` method now take a single argument which is an associative array of query string parameters.
* The `trace` method have been removed. Use `Gitlab\Api\Jobs::trace` instead.
* The `builds` method have been removed. Use `Gitlab\Api\Jobs::all` instead.
* The `build` method have been removed. Use `Gitlab\Api\Jobs::show` instead.
* The `events` method second and subsequent arguments have been replaced by a single associative array of query string parameters.
* The `deployments` method second and subsequent arguments have been replaced by a single associative array of query string parameters.

## `Gitlab\Api\ProjectNamespaces` changes

* The `search` method have been removed. Use `all` method instead.
* The `all` method now take a single argument which is an associative array of query string parameters.

## `Gitlab\Api\Repositories` changes

* The `commitBuilds` method have been removed. Use `Gitlab\Api\Projects::pipelines` instead.
* The `commits` method second and subsequent arguments have been replaced by a single associative array of query string parameters.

## `Gitlab\Model\Project` changes

* The `keys`, `key`, `addKey`, `removeKey`, `disableKey` and `enableKey` methods have been removed.
Use the `deployKeys`, `deployKey`, `addDeployKey`, `deleteDeployKey`, `removeDeployKey` and `enableDeployKey` instead.

## `Gitlab\Model\Snippet` changes

The `expires_at` property have been removed.`
