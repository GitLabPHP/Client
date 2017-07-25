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

## `Gitlab\Api\Issues` changes

* The second argument of `update`, `remove`, `showComments`, `showComment`, `addComment`, `updateComment`, `removeComment`,
 `setTimeEstimate`, `resetTimeEstimate`, `addSpentTime` and `resetSpentTime` methods is now a scoped issue id (iid).

## `Gitlab\Api\Projects` changes

* The `keys`, `key`, `addKey`, `removeKey`, `disableKey` and `enableKey` methods have been removed.
Use the `deployKeys`, `deployKey`, `addDeployKey`, `deleteDeployKey`, `removeDeployKey` and `enableDeployKey` instead.

## `Gitlab\Model\Project` changes

* The `keys`, `key`, `addKey`, `removeKey`, `disableKey` and `enableKey` methods have been removed.
Use the `deployKeys`, `deployKey`, `addDeployKey`, `deleteDeployKey`, `removeDeployKey` and `enableDeployKey` instead.

## `Gitlab\Model\Snippet` changes

The `expires_at` property have been removed.`
