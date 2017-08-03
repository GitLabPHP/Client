A PHP wrapper to be used with [Gitlab's API](https://github.com/gitlabhq/gitlabhq/tree/master/doc/api).
==============

[![Build Status](https://travis-ci.org/m4tthumphrey/php-gitlab-api.svg?branch=master)](https://travis-ci.org/m4tthumphrey/php-gitlab-api)

Based on [php-github-api](https://github.com/m4tthumphrey/php-github-api) and code from [KnpLabs](https://github.com/KnpLabs/php-github-api).

Installation
------------

Via [composer](https://getcomposer.org)

```bash
composer require m4tthumphrey/php-gitlab-api php-http/guzzle6-adapter
```

Why `php-http/guzzle6-adapter`? We are decoupled from any HTTP messaging client with help by [HTTPlug](http://httplug.io).

You can visit [HTTPlug for library users](http://docs.php-http.org/en/latest/httplug/users.html) to get more information about installing HTTPlug related packages.

Versioning
----------

Depending on your Gitlab server version, you must choose the right version of this library.
Please refer to the following table to pick the right one.

|Version|Gitlab API Version|Gitlab Version|
|-------|------------------|--------------|
|9.x    | V4               | >= 9.0       |
|8.x    | V3               | < 9.5        |

General API Usage
-----------------

```php
$client = \Gitlab\Client::create('http://git.yourdomain.com')
    ->authenticate('your_gitlab_token_here', \Gitlab\Client::AUTH_URL_TOKEN)
;

$project = $client->api('projects')->create('My Project', array(
  'description' => 'This is a project',
  'issues_enabled' => false
));

```

Model Usage
-----------

You can also use the library in an object oriented manner:

```php
$client = \Gitlab\Client::create('http://git.yourdomain.com')
    ->authenticate('your_gitlab_token_here', \Gitlab\Client::AUTH_URL_TOKEN)
;

# Creating a new project
$project = \Gitlab\Model\Project::create($client, 'My Project', array(
  'description' => 'This is my project',
  'issues_enabled' => false
));

$project->addHook('http://mydomain.com/hook/push/1');

# Creating a new issue
$project = new \Gitlab\Model\Project(1, $client);
$issue = $project->createIssue('This does not work.', array(
  'description' => 'This doesn\'t work properly. Please fix.',
  'assignee_id' => 2
));

# Closing that issue
$issue->close();
```

You get the idea! Take a look around ([API methods](https://github.com/m4tthumphrey/php-gitlab-api/tree/master/lib/Gitlab/Api),
[models](https://github.com/m4tthumphrey/php-gitlab-api/tree/master/lib/Gitlab/Model)) and please feel free to report any bugs.

Framework Integrations
----------------------
- **Symfony** - https://github.com/Zeichen32/GitLabApiBundle
- **Laravel** - https://github.com/vinkla/gitlab

If you have integrated GitLab into a popular PHP framework, let us know!

Contributing
------------

There are many parts of Gitlab that I have not added to this as it was originally created for personal use, hence the
lack of tests. Feel free to fork and add new functionality and tests, I'll gladly accept decent pull requests.
