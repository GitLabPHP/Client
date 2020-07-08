# GitLab PHP API Client

We present a modern [GitLab API v4](https://docs.gitlab.com/ce/api/) client for PHP.

![Banner](https://user-images.githubusercontent.com/2829600/86969006-fc2e3b00-c164-11ea-80b7-8750160a65c4.png)

<p align="center">
<a href="https://github.com/GitLabPHP/Client/actions?query=workflow%3ATests"><img src="https://img.shields.io/github/workflow/status/GitLabPHP/Client/Tests?label=Tests&style=flat-square" alt="Build Status"></img></a>
<a href="https://github.styleci.io/repos/6816335"><img src="https://github.styleci.io/repos/6816335/shield" alt="StyleCI Status"></img></a>
<a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-brightgreen?style=flat-square" alt="Software License"></img></a>
<a href="https://packagist.org/packages/m4tthumphrey/php-gitlab-api"><img src="https://img.shields.io/packagist/dt/m4tthumphrey/php-gitlab-api?style=flat-square" alt="Packagist Downloads"></img></a>
<a href="https://github.com/GitLabPHP/Client/releases"><img src="https://img.shields.io/github/release/GitLabPHP/Client?style=flat-square" alt="Latest Version"></img></a>
</p>

This is strongly based on [php-github-api](https://github.com/KnpLabs/php-github-api) by [KnpLabs](https://github.com/KnpLabs). With this in mind, we now have **very similar** clients for:

* [Bitbucket](https://bitbucket.org/) - [bitbucket/client](https://packagist.org/packages/bitbucket/client) by [Graham Campbell](https://github.com/GrahamCampbell).
* [GitHub](https://github.com/) - [knplabs/github-api](https://packagist.org/packages/knplabs/github-api) by [KnpLabs](https://github.com/KnpLabs/php-github-api).
* [GitLab](https://gitlab.com/) - [m4tthumphrey/php-gitlab-api](https://packagist.org/packages/m4tthumphrey/php-gitlab-api) which is this package!

Check out the [change log](CHANGELOG.md), [releases](https://github.com/GitLabPHP/Client/releases), [security policy](https://github.com/GitLabPHP/Client/security/policy), [license](LICENSE), [code of conduct](.github/CODE_OF_CONDUCT.md), and [contribution guidelines](.github/CONTRIBUTING.md).


## Installation

This version supports [PHP](https://php.net) 5.6-7.4. To get started, simply require the project using [Composer](https://getcomposer.org). You will also need to install any package that "provides" [`php-http/client-implementation`](https://packagist.org/providers/php-http/client-implementation).

### PHP 5.6+:

```
$ composer require m4tthumphrey/php-gitlab-api:^9.18 php-http/guzzle6-adapter:^2.0.1
```

### Laravel 5.5+:

```
$ composer require graham-campbell/gitlab:^2.7 php-http/guzzle6-adapter:^2.0.1
```

### Symfony 3+:

```
$ composer require zeichen32/gitlabapibundle:^2.6 php-http/guzzle6-adapter:^2.0.1
```

We are decoupled from any HTTP messaging client with help by [HTTPlug](http://httplug.io). You can visit [HTTPlug for library users](https://docs.php-http.org/en/latest/httplug/users.html) to get more information about installing HTTPlug related packages. [graham-campbell/gitlab](https://github.com/GrahamCampbell/Laravel-GitLab) is by [Graham Campbell](https://github.com/GrahamCampbell) and [zeichen32/gitlabapibundle](https://github.com/Zeichen32/GitLabApiBundle) is by [Jens Averkamp](https://github.com/Zeichen32).

## General API Usage

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

## Example with Pager

to fetch all your closed issue with pagination ( on the gitlab api )

```php
$client = Gitlab\Client::create('http://git.yourdomain.com')
    ->authenticate('your_gitlab_token_here', Gitlab\Client::AUTH_HTTP_TOKEN)
;

$pager = new Gitlab\ResultPager($client);
$issues = $pager->fetchAll($client->issues(), 'all', [null, ['state' => 'closed']]);

```

## Model Usage

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

## HTTP Client Builder

By providing a `Gitlab\HttpClient\Builder` to the `Gitlab\Client` constructor, you can customize the HTTP client. For example, to customize the user agent:

```php
$plugin = new Http\Client\Common\Plugin\HeaderSetPlugin([
    'User-Agent' => 'Foobar',
]);

$builder = new Gitlab\HttpClient\Builder();
$builder->addPlugin($plugin);

$client = new Gitlab\Client($builder);
```

One can read more about HTTPlug plugins [here](https://docs.php-http.org/en/latest/plugins/introduction.html#how-it-works). Take a look around ([API methods](https://github.com/GitLabPHP/Client/tree/9.18/lib/Gitlab/Api), [models](https://github.com/GitLabPHP/Client/tree/9.18/lib/Gitlab/Model)) and please feel free to report any bugs, noting our [code of conduct](.github/CODE_OF_CONDUCT.md).


## Contributing

We will gladly receive issue reports and review and accept pull requests, in accordance with our [code of conduct](.github/CODE_OF_CONDUCT.md) and [contribution guidelines](.github/CONTRIBUTING.md)!

```
$ make install
$ make test
```


## Security

If you discover a security vulnerability within this package, please send an email to Graham Campbell at graham@alt-three.com or Miguel Piedrafita at github@miguelpiedrafita.com. All security vulnerabilities will be promptly addressed. You may view our full security policy [here](https://github.com/GitLabPHP/Client/security/policy).


## License

GitLab PHP API Client is licensed under [The MIT License (MIT)](LICENSE).
