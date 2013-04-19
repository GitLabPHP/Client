A PHP Wrapper for use with the [Gitlab API](https://github.com/gitlabhq/gitlabhq/tree/master/doc/api).
==============

Based on [php-github-api](https://github.com/m4tthumphrey/php-github-api) and code from [KnpLabs](https://github.com/KnpLabs/php-github-api).

Installation
------------
Install Composer

```
$ curl -sS https://getcomposer.org/installer | php
$ sudo mv composer.phar /usr/local/bin/composer
```

Add the following to your require block in composer.json config:

```
"m4tthumphrey/php-gitlab-api": "dev-master"
```

Include Composer's autoloader:


```php
require_once dirname(__DIR__).'/vendor/autoload.php';
```

General API Usage
-----------------

```php
$client = new \Gitlab\Client('http://git.yourdomain.com/api/v3/'); // change here
$client->authenticate('your_gitlab_token_here', \Gitlab\Client::AUTH_URL_TOKEN); // change here

$project = $client->api('projects')->create('My Project', array(
  'description' => 'This is a project'
  'issues_enabled' => false
));

```

Model Usage
-----------

You can also use the library in an object oriented manner.

```php
$client = new \Gitlab\Client('http://git.yourdomain.com/api/v3/'); // change here
$client->authenticate('your_gitlab_token_here', \Gitlab\Client::AUTH_URL_TOKEN); // change here
```

Creating a new project

```php
$project = \Gitlab\Model\Project::create($client, 'My Project', array(
  'description' => 'This is my project',
  'issues_enabled' => false
));

$project->addHook('http://mydomain.com/hook/push/1');
```

Creating a new issue

```php
$project = new \Gitlab\Model\Project(1);
$project->setClient($client);
$issue = $project->createIssue('This does not work..', array(
  'description' => 'This doesnt work properly. Please fix',
  'assignee_id' => 2
));
```

Closing that issue

```php
$issue->close();
```

You get the idea! Take a look around and please feel free to report any bugs.

Contributing
------------

There are many parts of Gitlab that I have not added to this as it was originally created for personal use, hence the lack of tests. Feel free to fork and add new functionality and tests, I'll gladly accept decent pull requests.
