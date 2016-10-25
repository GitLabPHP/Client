A PHP wrapper to be used with [Gitlab's API](https://github.com/gitlabhq/gitlabhq/tree/master/doc/api).
==============

[![Build Status](https://travis-ci.org/m4tthumphrey/php-gitlab-api.svg?branch=master)](https://travis-ci.org/m4tthumphrey/php-gitlab-api)

Based on [php-github-api](https://github.com/m4tthumphrey/php-github-api) and code from [KnpLabs](https://github.com/KnpLabs/php-github-api).

Installation
------------
1. Install Composer

    ```bash
    $ curl -sS https://getcomposer.org/installer | php
    $ sudo mv composer.phar /usr/local/bin/composer
    ```

2. Add the following to your require block in composer.json config.

    > Note: be careful when using the `dev-master` tag as this may have unexpected results depending on your version of
    Gitlab. See the Versioning section below for more information.

    ```json
    "m4tthumphrey/php-gitlab-api": "dev-master"
    ```

3. Include Composer's autoloader:

    ```php
    require_once dirname(__DIR__).'/vendor/autoload.php';
    ```

Versioning
----------

From the 6.0 stable release of Gitlab, I shall now be matching the client version with the Gitlab version. For example
when Gitlab 6.1 is released I will release version 6.1.0 of the API client. If I need to make future updates to the client
before the next API version is released, I'll simply use a 3rd build version - `6.1.1`, `6.1.2` etc for example.

It is recommended that you keep your composer file in sync with whatever version of Gitlab you are currently running:
if you are using 6.0, you should require `6.0.*`; 6.1 should be `6.1.*`...

General API Usage
-----------------

```php
$client = new \Gitlab\Client('http://git.yourdomain.com/api/v3/');               // change here
$client->authenticate('your_gitlab_token_here', \Gitlab\Client::AUTH_URL_TOKEN); // change here

$project = $client->api('projects')->create('My Project', array(
  'description' => 'This is a project',
  'issues_enabled' => false
));

```

Model Usage
-----------

You can also use the library in an object oriented manner:

```php
$client = new \Gitlab\Client('http://git.yourdomain.com/api/v3/');               // change here
$client->authenticate('your_gitlab_token_here', \Gitlab\Client::AUTH_URL_TOKEN); // change here

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
