# Contributing to GitLab API client

## Workflow

* Fork the project.
* Make your bug fix or feature addition.
* Add tests for it. This is important so we don't break it in a future version unintentionally.
* Send a pull request. Bonus points for topic branches.

Due to time constraints, we are not always able to respond as quickly as we
would like. Please do not take delays personal and feel free to remind us if
you feel that we forgot to respond.

## Tests

A pre-requisite is that you install project dependencies using [composer]:

```
$ composer install
```

[composer]: https://getcomposer.org/

Running tests is simple as executing `phpunit`:

```
$ ./vendor/bin/phpunit
PHPUnit 7.5.20 by Sebastian Bergmann and contributors.

...............................................................  63 / 399 ( 15%)
............................................................... 126 / 399 ( 31%)
............................................................... 189 / 399 ( 47%)
............................................................... 252 / 399 ( 63%)
............................................................... 315 / 399 ( 78%)
............................................................... 378 / 399 ( 94%)
.....................                                           399 / 399 (100%)

Time: 823 ms, Memory: 14.00 MB

OK (399 tests, 903 assertions)
```
