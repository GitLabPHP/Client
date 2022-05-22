# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [11.8.0] - 2022-04-24

* Add support for `reviewer_id` and `wip` params in `MergeRequests::all()`
* Add support for `GroupEpics::issues()`
* Add support for `Projects::pipelineJobs()` and protected tags
* Add support for the confidential filter in `Issues:all()`
* Allow specifying params in `Wiki::showAll()`
* Allow specifying params in `SystemHooks::create()`
* Allow `chmod` action and `execute_filemode` attribute
* Implement group merge requests endpoints
* Implement event endpoints
* Add support for `Projects::patchProtectedBranch()`

[11.8.0]: https://github.com/GitLabPHP/Client/compare/11.7.1...11.8.0

## [11.7.1] - 2022-04-24

* Fixed `GroupsEpic::all()` method
* Fixed `Projects::createPipeline()` method

[11.7.1]: https://github.com/GitLabPHP/Client/compare/11.7.0...11.7.1

## [11.7.0] - 2022-01-24

* Dropped PHP 7.2 and 7.3 support

[11.7.0]: https://github.com/GitLabPHP/Client/compare/11.6.0...11.7.0

## [11.6.0] - 2022-01-23

* Added support for for workspace repository permissions
* Added support for `psr/cache:^3.0`

[11.6.0]: https://github.com/GitLabPHP/Client/compare/11.5.1...11.6.0

## [11.5.1] - 2022-01-23

* Fixed release API paths

[11.5.1]: https://github.com/GitLabPHP/Client/compare/11.5.0...11.5.1

## [11.5.0] - 2021-12-26

* Added support for filtering environments by state
* Added support for approval rules endpoints
* Added support for toggling the activate state of users
* Added support for managing packages
* Added support for filtering projects by topics
* Added support for locked merge requests
* Added support for filtering groups and projects by user
* Added support for removing protected branches
* Added support for `psr/cache:^2.0`
* Added support for `symfony/options-resolver:^6.0`
* Added support for PHP 8.1

[11.5.0]: https://github.com/GitLabPHP/Client/compare/11.4.1...11.5.0

## [11.4.1] - 2021-12-26

* Fixed creating environments
* Fixed double encoding of query parameters when comparing commits

[11.4.1]: https://github.com/GitLabPHP/Client/compare/11.4.0...11.4.1

## [11.4.0] - 2021-03-27

* Added parameters to the list of project repository tags
* Added support for the epics endpoints
* Added support for project access tokens
* Added support for reverting commits

[11.4.0]: https://github.com/GitLabPHP/Client/compare/11.3.0...11.4.0

## [11.3.0] - 2021-03-14

* Added support for disabling and enabling runners
* Added support for a single inherited members
* Added support for tag search

[11.3.0]: https://github.com/GitLabPHP/Client/compare/11.2.1...11.3.0

## [11.2.1] - 2021-03-14

* Fixed commit order validation

[11.2.1]: https://github.com/GitLabPHP/Client/compare/11.2.0...11.2.1

## [11.2.0] - 2021-02-20

* Added support for user memberships
* Added support for the following projects parameters: id_after, id_before, last_activity_after, last_activity_before, repository_checksum_failed, repository_storage, wiki_checksum_failed, with_custom_attributes, with_programming_language

[11.2.0]: https://github.com/GitLabPHP/Client/compare/11.1.0...11.2.0

## [11.1.0] - 2021-01-25

* Added CI schedule variables endpoints
* Added support for triggering a pipeline
* Added support for the search_namespaces projects parameter
* Added support for order_by and sort deployments parameters

[11.1.0]: https://github.com/GitLabPHP/Client/compare/11.0.0...11.1.0

## [11.0.0] - 2020-12-22

* Removed models API
* Dropped support for PHP 7.1
* Updated to latest labels API
* Made builder class final
* Re-worked pagination
* Client authenticate and setUrl now return void
* Added additional return type enforcement

[11.0.0]: https://github.com/GitLabPHP/Client/compare/10.4.0...11.0.0

## [10.4.0] - 2020-12-22

[10.4.0]: https://github.com/GitLabPHP/Client/compare/10.3.1...10.4.0

* Add min_access_level option to group search
* Added support for additional issue order clauses
* Added params array to remove user method to support hard_delete

## [10.3.1] - 2020-12-04

* Work around GitLab's API returning bad JSON for some endpoints

[10.3.1]: https://github.com/GitLabPHP/Client/compare/10.3.0...10.3.1

## [10.3.0] - 2020-11-27

* Support PHP 8.0

[10.3.0]: https://github.com/GitLabPHP/Client/compare/10.2.0...10.3.0

## [10.2.0] - 2020-11-09

* Added variable_type to addVariable and updateVariable
* Added get pipeline bridget jobs method

[10.2.0]: https://github.com/GitLabPHP/Client/compare/10.1.2...10.2.0

## [10.1.2] - 2020-11-09

* Fixed comparing repositories

[10.1.2]: https://github.com/GitLabPHP/Client/compare/10.1.1...10.1.2

## [10.1.1] - 2020-10-26

* Fixed phpdoc typo
* Fixed broken query builder

[10.1.1]: https://github.com/GitLabPHP/Client/compare/10.1.0...10.1.1

## [10.1.0] - 2020-10-24

* Added method to get protected branches for a project
* Added with_merge_status_recheck option for fetching MRs
* Added commit cherry-pick API
* Added support for optional Note parameters 
* Deprecated models API

[10.1.0]: https://github.com/GitLabPHP/Client/compare/10.0.1...10.1.0

## [10.0.1] - 2020-10-24

* Fixed using the name of a group as an ID
* Fixed various phpdoc issues
* Reverted query builder changes

[10.0.1]: https://github.com/GitLabPHP/Client/compare/10.0.0...10.0.1

## [10.0.0] - 2020-08-15

* Added void return types to void methods

[10.0.0]: https://github.com/GitLabPHP/Client/compare/10.0.0-RC2...10.0.0

## [10.0.0-RC2] - 2020-07-23

* Restored 9.x behaviour for empty JSON responses
* Support the issue link link_type parameter

[10.0.0-RC2]: https://github.com/GitLabPHP/Client/compare/10.0.0-RC1...10.0.0-RC2

## [10.0.0-RC1] - 2020-07-22

* Removed all deprecated functionality
* Switched to PSR-17 and PSR-18
* Encode URIs according to RFC 3986
* Send request bodies as JSON to GitLab
* Redesigned pagination
* Added array types where missing
* Added scalar param types
* Added user events API

[10.0.0-RC1]: https://github.com/GitLabPHP/Client/compare/9.18.1...10.0.0-RC1

## [9.18.1] - 2020-07-22

* Fixed error in getHeader function
* Fixed incorrect param type doc

[9.18.1]: https://github.com/GitLabPHP/Client/compare/9.18.0...9.18.1

## [9.18.0] - 2020-07-11

* Deprecated all APIs that are deprecated or removed as of GitLab 13.1
* Deprecated old authentication methods and deprecated not specifying an authentication mode
* Deprecated dynamic property access on the client, `Client::api()`, `Client::create()`, and `Client::getResponseHistory()`
* Deprecated passing a stream factory to the Api classes: get it from the client instance instead
* Soft marked various classes as final and/or internal
* Added support for HTTP caching
* Implement removing award emojis
* Implemented notes APIs
* Extended pipeline APIs
* Extended MR approvals APIs
* Add subscribe/unsubscribe methods to issue API
* Add scope and allow all projects to MR API
* Add method to access project discussions
* Update parameters for repository/commits APIs
* Added delete merged branches API function
* Allow to search and find issues by "assignee_id"
* Updated Issues to support updated_after

[9.18.0]: https://github.com/GitLabPHP/Client/compare/9.17.1...9.18.0

## [9.17.1] - 2020-02-17

* Fixed text encoding for `Repositories::createCommit()`
* Corrected lots of phpdoc errors and edges cases

[9.17.1]: https://github.com/GitLabPHP/Client/compare/9.17.0...9.17.1

## [9.17.0] - 2020-02-17

* Added support for the wiki APIs
* Implemented `Environments::show()`
* Implemented `Issues::showParticipants()`
* Add method to get issues for a group
* Add forks API call to return all forked projects
* Added users projects request parameters normalization

[9.17.0]: https://github.com/GitLabPHP/Client/compare/9.16.0...9.17.0
