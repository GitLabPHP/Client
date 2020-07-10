CHANGE LOG
==========


## 9.18.0 (Upcoming)

* Deprecated all APIs that are deprecated or removed as of GitLab 13.1
* Deprecated old authentication methods and deprecated not specifying an authentication mode
* Deprecated dynamic property access on the client, `Client::api()`, `Client::create()`, and `Client::getResponseHistory()`
* Soft marked various classes as final


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
