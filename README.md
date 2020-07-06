PHP GitLab API
==============

We present a PHP client for [GitLab](https://gitlab.com/)'s [API v4](https://gitlab.com/gitlab-org/gitlab/-/tree/master/doc/api).

[![Build Status](
https://img.shields.io/github/workflow/status/GitLabPHP/Client/tests?style=flat-square)](https://github.com/GitLabPHP/Client/actions?query=workflow%3ATests)
[![StyleCI](https://github.styleci.io/repos/6816335/shield?branch=10.0)](https://github.styleci.io/repos/6816335?branch=10.0)
[![Latest Stable Version](https://poser.pugx.org/m4tthumphrey/php-gitlab-api/version?format=flat-square)](https://packagist.org/packages/m4tthumphrey/php-gitlab-api)
[![Total Downloads](https://poser.pugx.org/m4tthumphrey/php-gitlab-api/downloads?format=flat-square)](https://packagist.org/packages/m4tthumphrey/php-gitlab-api)
[![License](https://poser.pugx.org/m4tthumphrey/php-gitlab-api/license?format=flat-square)](https://packagist.org/packages/m4tthumphrey/php-gitlab-api)

This is strongly based on [php-github-api](https://github.com/KnpLabs/php-github-api) by [KnpLabs](https://github.com/KnpLabs). With this in mind, we now have **very similar** clients for:

* [Bitbucket](https://bitbucket.org/) - [bitbucket/client](https://packagist.org/packages/bitbucket/client) by [Graham Campbell](https://github.com/GrahamCampbell).
* [GitHub](https://github.com/) - [knplabs/github-api](https://packagist.org/packages/knplabs/github-api) by [KnpLabs](https://github.com/KnpLabs/php-github-api).
* [GitLab](https://gitlab.com/) - [m4tthumphrey/php-gitlab-api](https://packagist.org/packages/m4tthumphrey/php-gitlab-api) which is this package!

Installation
------------

This version supports [PHP](https://php.net) 7.1-7.4. To get started, simply require the project using [Composer](https://getcomposer.org). You will also need to install packages that "provide" [`psr/http-client-implementation`](https://packagist.org/providers/psr/http-client-implementation) and [`psr/http-factory-implementation`](https://packagist.org/providers/psr/http-factory-implementation).

### PHP 7.1+:

```bash
$ composer require m4tthumphrey/php-gitlab-api:^10.0 php-http/guzzle6-adapter:^2.0.1 http-interop/http-factory-guzzle:^1.0
```

### PHP 7.2+:

```bash
$ composer require m4tthumphrey/php-gitlab-api:^10.0 guzzlehttp/guzzle:^7.0.1 http-interop/http-factory-guzzle:^1.0
```

### Laravel 6+:

```bash
$ composer require graham-campbell/gitlab:^4.0 guzzlehttp/guzzle:^7.0.1 http-interop/http-factory-guzzle:^1.0
```

### Symfony 4:

```bash
$ composer require zeichen32/gitlabapibundle:^5.0 symfony/http-client:^4.4 nyholm/psr7:^1.3
```

### Symfony 5:

```bash
$ composer require zeichen32/gitlabapibundle:^5.0 symfony/http-client:^5.0 nyholm/psr7:^1.3
```

We are decoupled from any HTTP messaging client with help by [HTTPlug](http://httplug.io). You can visit [HTTPlug for library users](https://docs.php-http.org/en/latest/httplug/users.html) to get more information about installing HTTPlug related packages. [graham-campbell/gitlab](https://github.com/GrahamCampbell/Laravel-GitLab) is by [Graham Campbell](https://github.com/GrahamCampbell) and [zeichen32/gitlabapibundle](https://github.com/Zeichen32/GitLabApiBundle) is by [Jens Averkamp](https://github.com/Zeichen32).

General API Usage
-----------------

```php
// Token authentication
$client = Gitlab\Client::create('http://git.yourdomain.com')
    ->authenticate('your_gitlab_token_here', Gitlab\Client::AUTH_HTTP_TOKEN)
;

// OAuth2 authentication
$client = Gitlab\Client::create('http://gitlab.yourdomain.com')
    ->authenticate('your_gitlab_token_here', Gitlab\Client::AUTH_OAUTH_TOKEN)
;

$project = $client->projects()->create('My Project', [
    'description' => 'This is a project',
    'issues_enabled' => false,
]);

```

Example with Pager
------------------

to fetch all your closed issue with pagination ( on the gitlab api )

```php
$client = Gitlab\Client::create('http://git.yourdomain.com')
    ->authenticate('your_gitlab_token_here', Gitlab\Client::AUTH_HTTP_TOKEN)
;

$pager = new Gitlab\ResultPager($client);
$issues = $pager->fetchAll($client->issues(), 'all', [null, ['state' => 'closed']]);

```

Model Usage
-----------

You can also use the library in an object oriented manner:

```php
$client = Gitlab\Client::create('http://git.yourdomain.com')
    ->authenticate('your_gitlab_token_here', Gitlab\Client::AUTH_HTTP_TOKEN)
;

// Creating a new project
$project = Gitlab\Model\Project::create($client, 'My Project', [
    'description' => 'This is my project',
    'issues_enabled' => false,
]);

$project->addHook('http://mydomain.com/hook/push/1');

// Creating a new issue
$project = new Gitlab\Model\Project(1, $client);
$issue = $project->createIssue('This does not work.', [
    'description' => 'This doesn\'t work properly. Please fix.',
    'assignee_id' => 2,
]);

// Closing that issue
$issue->close();
```

The HTTP Client Builder
-----------------------

By providing a `Gitlab\HttpClient\Builder` to the `Gitlab\Client` constructor, you can customize the HTTP client. For example, to customize the user agent:

```php
$plugin = new Http\Client\Common\Plugin\HeaderSetPlugin([
    'User-Agent' => 'Foobar',
]);

$builder = new Gitlab\HttpClient\Builder();
$builder->addPlugin($plugin);

$client = new Gitlab\Client($builder);
```

One can read more about HTTPlug plugins [here](https://docs.php-http.org/en/latest/plugins/introduction.html#how-it-works). Take a look around ([API methods](https://github.com/GitLabPHP/Client/tree/10.0/lib/Gitlab/Api), [models](https://github.com/GitLabPHP/Client/tree/10.0/lib/Gitlab/Model)) and please feel free to report any bugs, noting our [code of conduct](.github/CODE_OF_CONDUCT.md).

Contributing
------------

Not all endpoints of the API are implemented yet. We will gladly review and accept pull requests, in accordance with our [contribution guidelines](.github/CONTRIBUTING.md)!

```bash
$ make install
$ make test
```

Security
--------

If you discover a security vulnerability within this package, please send an email to Graham Campbell at graham@alt-three.com or Miguel Piedrafita at github@miguelpiedrafita.com. All security vulnerabilities will be promptly addressed. You may view our full security policy [here](https://github.com/GitLabPHP/Client/security/policy).

License
-------

PHP GitLab API is licensed under [The MIT License (MIT)](LICENSE).
