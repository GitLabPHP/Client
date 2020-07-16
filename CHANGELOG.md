CHANGE LOG
==========


## 10.0.0 (UPCOMING)

* Removed all deprecated functionality
* Switched to PSR-17 and PSR-18
* Encode URIs according to RFC 3986
* Send request bodies as JSON to GitLab
* Redesigned pagination


## 9.19.0 (UPCOMING)

* Added user events API


## 9.18.0 (11/07/2020)

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


## 9.17.1 (17/02/2020)

* Fixed text encoding for `Repositories::createCommit()`
* Corrected lots of phpdoc errors and edges cases


## 9.17.0 (17/02/2020)

* Added support for the wiki APIs
* Implemented `Environments::show()`
* Implemented `Issues::showParticipants()`
* Add method to get issues for a group
* Add forks API call to return all forked projects
* Added users projects request parameters normalization
