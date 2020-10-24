CHANGE LOG
==========


## 11.0.0-RC1 (UPCOMING)

* Removed models API


## 10.2.0 (UPCOMING)


## 10.1.0 (24/10/2020)

* Added method to get protected branches for a project
* Added with_merge_status_recheck option for fetching MRs
* Added commit cherry-pick API
* Added support for optional Note parameters 
* Deprecated models API


## 10.0.1 (24/10/2020)

* Fixed using the name of a group as an ID
* Fixed various phpdoc issues
* Reverted query builder changes


## 10.0.0 (15/08/2020)

* Added void return types to void methods


## 10.0.0-RC2 (23/07/2020)

* Restored 9.x behaviour for empty JSON responses
* Support the issue link link_type parameter


## 10.0.0-RC1 (22/07/2020)

* Removed all deprecated functionality
* Switched to PSR-17 and PSR-18
* Encode URIs according to RFC 3986
* Send request bodies as JSON to GitLab
* Redesigned pagination
* Added array types where missing
* Added scalar param types
* Added user events API


## 9.18.1 (22/07/2020)

* Fixed error in getHeader function
* Fixed incorrect param type doc


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
