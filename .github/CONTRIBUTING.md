# CONTRIBUTION GUIDELINES

Contributions are **welcome** and will be fully **credited**.

We accept contributions via pull requests on Github. Please review these guidelines before continuing.

## Guidelines

* Please follow the [Symfony Coding Standards](https://symfony.com/doc/current/contributing/code/standards.html), enforced by [StyleCI](https://styleci.io/).
* Ensure that the current tests pass, and if you've added something new, add the tests where relevant.
* Send a coherent commit history, making sure each individual commit in your pull request is meaningful.
* You may need to [rebase](https://git-scm.com/book/en/v2/Git-Branching-Rebasing) to avoid merge conflicts.
* If you are changing or adding to the behaviour or public api, you may need to update the docs.
* Please remember that we follow [SemVer](https://semver.org/).

## Running Tests

First, install the dependencies:

```bash
$ make install
```

Then run the test suite and static analyzers:

```bash
$ make test
```

* The tests will be automatically run by [GitHub Actions](https://github.com/features/actions) against pull requests.
* We also have [StyleCI](https://styleci.io/) setup to automatically fix any code style issues.
